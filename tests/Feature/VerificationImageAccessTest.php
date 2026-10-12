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

        $student = User::create([
            'name' => 'Test Student',
            'email' => 'student@example.test',
            'password' => 'password',
            'role' => 'student',
            'selfie_image_path' => 'verification/selfie/student.png',
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
        ]);

        $this->actingAs($admin)
            ->get(route('profile.verification-image', $student))
            ->assertOk();

        $this->actingAs($student)
            ->get(route('profile.verification-image', $student))
            ->assertOk();

        $this->actingAs($otherStudent)
            ->get(route('profile.verification-image', $student))
            ->assertForbidden();

        $this->actingAs($student)
            ->post(route('profile.photo.update'), [
                'profile_photo' => UploadedFile::fake()->image('profile.png'),
            ])
            ->assertRedirect();

        $student->refresh();
        $this->assertNotEmpty($student->profile_photo_path);
        Storage::disk('public')->assertExists($student->profile_photo_path);

        $this->actingAs($student)
            ->post(route('profile.verification-image.update'), [
                'verification_image' => UploadedFile::fake()->image('student-id.png'),
            ])
            ->assertRedirect();

        $student->refresh();
        $this->assertNotEmpty($student->id_image_path);
        Storage::disk('local')->assertExists($student->id_image_path);
        Storage::disk('public')->assertMissing($student->id_image_path);

        $this->actingAs($admin)
            ->get(route('profile.verification-image', $student))
            ->assertOk();
    }
}
