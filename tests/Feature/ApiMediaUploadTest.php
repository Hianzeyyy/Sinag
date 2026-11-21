<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\FakesCloudinaryMedia;
use Tests\TestCase;

class ApiMediaUploadTest extends TestCase
{
    use FakesCloudinaryMedia;

    public function test_api_registration_stores_profile_and_verification_images_on_cloudinary(): void
    {
        $this->fakeCloudinaryMedia();
        $this->createUsersTable();

        $this->post('/api/register', [
            'name' => 'Test Student',
            'email' => '21student@example.test',
            'account_type' => 'student',
            'age' => 20,
            'gender' => 'Female',
            'department' => 'BS Information Technology',
            'profile_photo' => UploadedFile::fake()->image('profile.png')->size(6 * 1024),
            'selfie_image' => UploadedFile::fake()->image('student-id.png')->size(6 * 1024),
            'password' => 'strong-password',
            'password_confirmation' => 'strong-password',
        ])->assertCreated();

        $user = DB::table('users')->where('email', '21student@example.test')->first();
        $this->assertStringStartsWith('cloudinary://upload/image/sinag/profile-photos/', $user->profile_photo_path);
        $this->assertStringStartsWith('cloudinary://authenticated/image/sinag/verification/selfie/', $user->selfie_image_path);
        $this->assertSame($user->selfie_image_path, $user->id_image_path);
    }

    public function test_api_profile_photo_and_password_reset_id_uploads_use_cloudinary(): void
    {
        $this->fakeCloudinaryMedia();
        $this->createUsersTable();
        Schema::create('password_reset_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->string('reference_number')->unique();
            $table->string('email');
            $table->string('name');
            $table->string('student_employee_id');
            $table->string('department')->nullable();
            $table->string('id_picture_path');
            $table->text('reason')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });

        $user = User::create([
            'name' => 'Test Student',
            'email' => 'student@example.test',
            'password' => 'password',
            'role' => 'student',
            'account_status' => 'active',
        ]);

        $this->actingAs($user, 'sanctum')
            ->post('/api/profile/photo', [
                'profile_photo' => UploadedFile::fake()->image('new-profile.png'),
            ])
            ->assertOk();
        $this->assertStringStartsWith(
            'cloudinary://upload/image/sinag/profile-photos/',
            $user->fresh()->profile_photo_path
        );

        $this->post('/api/forgot-password/request', [
            'email' => $user->email,
            'student_employee_id' => 'STU-100',
            'name' => $user->name,
            'id_picture' => UploadedFile::fake()->image('student-id.png'),
        ])->assertCreated();
        $this->assertStringStartsWith(
            'cloudinary://authenticated/image/sinag/password-reset-requests/',
            DB::table('password_reset_requests')->value('id_picture_path')
        );
    }

    private function createUsersTable(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('student');
            $table->string('account_type')->nullable();
            $table->string('employee_category')->nullable();
            $table->string('account_status')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->unsignedTinyInteger('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('department')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('cloak_alias')->nullable();
            $table->string('profile_photo_path')->nullable();
            $table->string('id_image_path')->nullable();
            $table->string('selfie_image_path')->nullable();
            $table->timestamps();
        });
    }
}
