<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegistrationNameTest extends TestCase
{
    public function test_registration_stores_first_middle_and_last_name_and_composes_the_display_name(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->unsignedTinyInteger('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('department')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('password');
            $table->string('role');
            $table->string('account_type')->nullable();
            $table->string('employee_category')->nullable();
            $table->string('cloak_alias')->nullable();
            $table->string('profile_photo_path')->nullable();
            $table->string('selfie_image_path')->nullable();
            $table->string('account_status')->nullable();
            $table->timestamps();
        });
        Storage::fake('local');
        Storage::fake('public');

        $response = $this->post(route('register'), [
            'first_name' => 'Ava',
            'middle_name' => 'Marie',
            'last_name' => 'Santos',
            'email' => '21ava@example.test',
            'account_type' => 'student',
            'phone_number' => '09123456789',
            'age' => 20,
            'gender' => 'Female',
            'department' => 'BS Information Technology (BSIT)',
            'profile_photo' => UploadedFile::fake()->image('profile.png'),
            'selfie_image' => UploadedFile::fake()->image('student-id.png'),
            'password' => 'strong-password',
            'password_confirmation' => 'strong-password',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('users', [
            'name' => 'Ava Marie Santos',
            'first_name' => 'Ava',
            'middle_name' => 'Marie',
            'last_name' => 'Santos',
            'email' => '21ava@example.test',
        ]);

        $user = User::query()->where('email', '21ava@example.test')->firstOrFail();
        $this->assertSame('Ava Marie Santos', $user->name);

        $this->post(route('register'), [
            'first_name' => 'Lia',
            'middle_name' => '',
            'last_name' => 'Reyes',
            'email' => '22lia@example.test',
            'account_type' => 'student',
            'phone_number' => '09123456780',
            'age' => 21,
            'gender' => 'Female',
            'department' => 'BS Information Technology (BSIT)',
            'profile_photo' => UploadedFile::fake()->image('profile-optional.png'),
            'selfie_image' => UploadedFile::fake()->image('student-id-optional.png'),
            'password' => 'strong-password',
            'password_confirmation' => 'strong-password',
        ])->assertRedirect(route('login'));

        $this->assertDatabaseHas('users', [
            'name' => 'Lia Reyes',
            'first_name' => 'Lia',
            'middle_name' => null,
            'last_name' => 'Reyes',
            'email' => '22lia@example.test',
        ]);
    }

    public function test_registration_form_has_required_first_and_last_names_and_optional_middle_name(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('name="first_name"', false)
            ->assertSee('name="middle_name"', false)
            ->assertSee('name="last_name"', false)
            ->assertSee('Middle Name <span class="text-muted">(optional)</span>', false);
    }
}
