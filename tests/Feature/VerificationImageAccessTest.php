<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
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
            $table->timestamps();
        });

        Storage::fake('local');
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
    }
}
