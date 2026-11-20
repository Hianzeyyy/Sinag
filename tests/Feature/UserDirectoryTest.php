<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserDirectoryTest extends TestCase
{
    public function test_active_users_can_view_profile_photos_in_directory_without_private_images(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('student');
            $table->string('account_status')->nullable();
            $table->string('cloak_alias')->nullable();
            $table->string('department')->nullable();
            $table->string('profile_photo_path')->nullable();
            $table->string('id_image_path')->nullable();
            $table->timestamps();
        });

        $viewer = User::create([
            'name' => 'Viewer',
            'email' => 'viewer@example.test',
            'password' => 'password',
            'role' => 'student',
            'account_status' => 'active',
        ]);
        $member = User::create([
            'name' => 'Private Name',
            'email' => 'member@example.test',
            'password' => 'password',
            'role' => 'student',
            'account_status' => 'approved',
            'cloak_alias' => 'Community Member',
            'department' => 'Education',
            'profile_photo_path' => 'profile-photos/member.png',
            'id_image_path' => 'verification/id/private.png',
        ]);
        User::create([
            'name' => 'Pending Member',
            'email' => 'pending@example.test',
            'password' => 'password',
            'role' => 'student',
            'account_status' => 'pending',
        ]);
        $employee = User::create([
            'name' => 'Employee Viewer',
            'email' => 'employee@example.test',
            'password' => 'password',
            'role' => 'employee',
            'account_status' => 'active',
        ]);

        $this->actingAs($viewer)
            ->get(route('users.directory'))
            ->assertOk()
            ->assertSeeText('Community Directory')
            ->assertDontSee('href="' . route('users.directory') . '"', false)
            ->assertSeeText('Suggestions')
            ->assertSeeText('Community Member')
            ->assertSee(route('profile.photo', $member), false)
            ->assertDontSeeText('Private Name')
            ->assertDontSee('verification/id/private.png', false)
            ->assertDontSeeText('Pending Member');

        $this->actingAs($employee)
            ->get(route('users.directory'))
            ->assertOk()
            ->assertDontSee('href="' . route('users.directory') . '"', false)
            ->assertSeeText('Suggestions');
    }

    public function test_directory_requires_authentication(): void
    {
        $this->get(route('users.directory'))
            ->assertRedirect(route('login'));
    }
}
