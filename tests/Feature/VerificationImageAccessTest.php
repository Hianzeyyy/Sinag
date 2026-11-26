<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\MediaResolver;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\UploadedFile;
use Tests\Concerns\FakesCloudinaryMedia;
use Tests\TestCase;

class VerificationImageAccessTest extends TestCase
{
    use FakesCloudinaryMedia;

    public function test_cloudinary_private_delivery_errors_are_not_reported_as_missing_files(): void
    {
        $this->fakeCloudinaryMedia(false);
        Http::fake([
            'https://res.cloudinary.com/*' => Http::response('unavailable', 503),
        ]);

        $response = MediaResolver::fileResponse(
            'cloudinary://authenticated/image/sinag/verification/id/test-image'
        );

        $this->assertNotNull($response);
        $this->assertSame(502, $response->getStatusCode());
        $this->assertStringContainsString('private', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
    }

    public function test_authenticated_video_delivery_forwards_range_requests(): void
    {
        $this->fakeCloudinaryMedia(false);
        Http::fake([
            'https://res.cloudinary.com/*' => Http::response('video bytes', 206, [
                'Content-Type' => 'video/mp4',
                'Content-Length' => '11',
                'Content-Range' => 'bytes 0-10/100',
                'Accept-Ranges' => 'bytes',
            ]),
        ]);
        request()->headers->set('Range', 'bytes=0-10');

        $response = app(\App\Support\CloudinaryMedia::class)->fileResponse(
            'cloudinary://authenticated/video/sinag/reports/evidence/test-video'
        );

        $this->assertNotNull($response);
        $this->assertSame(206, $response->getStatusCode());
        $this->assertSame('video/mp4', $response->headers->get('Content-Type'));
        $this->assertSame('bytes 0-10/100', $response->headers->get('Content-Range'));
        $this->assertSame('bytes', $response->headers->get('Accept-Ranges'));
        Http::assertSent(fn ($request): bool => $request->hasHeader('Range', 'bytes=0-10'));
    }

    public function test_verification_image_is_available_to_owner_and_admin_only(): void
    {
        $this->fakeCloudinaryMedia();

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('student');
            $table->string('cloak_alias')->nullable();
            $table->string('id_image_path')->nullable();
            $table->string('selfie_image_path')->nullable();
            $table->string('profile_photo_path')->nullable();
            $table->timestamps();
        });

        Storage::fake('local');
        Storage::fake('public');
        Storage::disk('local')->put('verification/selfie/student.png', 'test image');
        Storage::disk('local')->put('verification/id/student.png', 'test ID image');
        Storage::disk('public')->put('profile-photos/student.png', 'test profile image');

        $student = User::create([
            'name' => 'Test Student',
            'email' => 'student@example.test',
            'password' => 'password',
            'role' => 'student',
            'selfie_image_path' => 'verification/selfie/student.png',
            'id_image_path' => 'C:\\legacy-app\\storage\\app\\private\\verification\\id\\student.png',
            'profile_photo_path' => 'C:\\legacy-app\\storage\\app\\public\\profile-photos\\student.png',
        ]);

        $admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $otherStudent = User::create([
            'name' => 'Other Student',
            'email' => 'other@example.test',
            'password' => 'password',
            'role' => 'student',
            'profile_photo_path' => 'profile-photos/missing.png',
        ]);

        foreach ([$admin, $otherStudent] as $user) {
            $avatarResponse = $this->get(route('profile.photo', $user))->assertOk();
            $this->assertStringContainsString('image/svg+xml', $avatarResponse->headers->get('Content-Type'));
            $this->assertStringContainsString(mb_substr($user->name, 0, 1), $avatarResponse->getContent());
        }

        $idImageResponse = $this->actingAs($admin)
            ->get(route('profile.verification-image', $student))
            ->assertOk();
        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\BinaryFileResponse::class, $idImageResponse->baseResponse);
        $this->assertSame(
            Storage::disk('local')->path('verification/id/student.png'),
            $idImageResponse->baseResponse->getFile()->getPathname()
        );

        $this->actingAs($student)
            ->get(route('profile.verification-image', $student))
            ->assertOk();

        $this->actingAs($otherStudent)
            ->get(route('profile.verification-image', $student))
            ->assertForbidden();

        $photoResponse = $this->actingAs($otherStudent)
            ->get(route('profile.photo', $student))
            ->assertOk();
        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\BinaryFileResponse::class, $photoResponse->baseResponse);
        $this->assertSame(
            Storage::disk('public')->path('profile-photos/student.png'),
            $photoResponse->baseResponse->getFile()->getPathname()
        );
        $this->assertStringContainsString('private', $photoResponse->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-store', $photoResponse->headers->get('Cache-Control'));

        $this->actingAs($student)
            ->post(route('profile.photo.update'), [
                'profile_photo' => UploadedFile::fake()->image('profile.png'),
            ])
            ->assertRedirect();

        $student->refresh();
        $this->assertNotEmpty($student->profile_photo_path);
        $this->assertStringStartsWith('cloudinary://upload/image/sinag/profile-photos/', $student->profile_photo_path);

        $this->actingAs($student)
            ->post(route('profile.verification-image.update'), [
                'verification_image' => UploadedFile::fake()->image('student-id.png'),
            ])
            ->assertRedirect();

        $student->refresh();
        $this->assertNotEmpty($student->id_image_path);
        $this->assertStringStartsWith('cloudinary://authenticated/image/sinag/verification/id/', $student->id_image_path);

        $this->actingAs($otherStudent)
            ->get(route('profile.verification-image', $student))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('profile.verification-image', $student))
            ->assertOk();

        $publicPhotoResponse = $this->actingAs($otherStudent)
            ->get(route('profile.photo', $student))
            ->assertRedirect();
        $this->assertStringContainsString('/image/upload/', $publicPhotoResponse->headers->get('Location'));
    }
}
