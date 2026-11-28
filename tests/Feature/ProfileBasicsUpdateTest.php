<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProfileBasicsUpdateTest extends TestCase
{
    public function test_profile_basics_are_editable_without_changing_email(): void
    {
        $this->createUsersTable();
        $user = $this->createUser();

        $this->actingAs($user)
            ->post(route('profile.update-basics'), [
                'name' => 'Updated Student Name',
                'email' => 'changed@example.test',
                'age' => 22,
                'gender' => 'Female',
                'department' => 'BS Information Technology',
                'phone_number' => '09123456780',
                'cloak_alias' => 'StudentNew',
            ])
            ->assertRedirect(route('profile.show'))
            ->assertSessionHas('status', 'Profile information updated.');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Student Name',
            'email' => 'student@example.test',
            'first_name' => 'Updated',
            'middle_name' => 'Student',
            'last_name' => 'Name',
            'age' => 22,
            'gender' => 'Female',
            'department' => 'BS Information Technology',
            'phone_number' => '09123456780',
            'cloak_alias' => 'StudentNew',
        ]);
        $this->assertSame('Updated Student Name', User::findOrFail($user->id)->name);
    }

    public function test_profile_rejects_phone_numbers_longer_than_eleven_digits(): void
    {
        $this->createUsersTable();
        $user = $this->createUser();

        $this->actingAs($user)
            ->from(route('profile.show'))
            ->post(route('profile.update-basics'), [
                'name' => 'Updated Student Name',
                'age' => 22,
                'gender' => 'Female',
                'department' => 'BS Information Technology',
                'phone_number' => '091234567890',
                'cloak_alias' => 'StudentNew',
            ])
            ->assertRedirect(route('profile.show'))
            ->assertSessionHasErrors('phone_number');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Student User',
            'phone_number' => '09123456789',
        ]);
    }

    public function test_profile_page_links_to_the_full_personal_data_editor(): void
    {
        $this->createUsersTable();
        $user = $this->createUser();

        $this->actingAs($user)
            ->get(route('profile.show'))
            ->assertOk()
            ->assertSee('Edit Basic Information')
            ->assertSee('Edit Full Profile')
            ->assertSee(route('personal-data.edit'), false);
    }

    private function createUsersTable(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('student');
            $table->string('account_status')->default('active');
            $table->unsignedTinyInteger('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('department')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('cloak_alias')->nullable();
            $table->timestamps();
        });
    }

    private function createUser(): User
    {
        return User::create([
            'name' => 'Student User',
            'first_name' => 'Student',
            'last_name' => 'User',
            'email' => 'student@example.test',
            'password' => 'password',
            'role' => 'student',
            'account_status' => 'active',
            'age' => 21,
            'gender' => 'Male',
            'department' => 'BS Information Technology',
            'phone_number' => '09123456789',
            'cloak_alias' => 'StudentOld',
        ]);
    }
}
