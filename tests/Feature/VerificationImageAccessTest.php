<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class VerificationImageAccessTest extends TestCase
{
    public function test_verification_image_is_available_to_owner_and_admin_only(): void
    {
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
        $this->assertTrue(Storage::disk('public')->exists($student->profile_photo_path));

        $this->actingAs($student)
            ->post(route('profile.verification-image.update'), [
                'verification_image' => UploadedFile::fake()->image('student-id.png'),
            ])
            ->assertRedirect();

        $student->refresh();
        $this->assertNotEmpty($student->id_image_path);
        $this->assertTrue(Storage::disk('local')->exists($student->id_image_path));
        $this->assertFalse(Storage::disk('public')->exists($student->id_image_path));

        $this->actingAs($admin)
            ->get(route('profile.verification-image', $student))
            ->assertOk();
    }
}
