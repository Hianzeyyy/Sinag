<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PasswordAttemptLimitTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('student');
            $table->string('account_status')->default('active');
            $table->unsignedTinyInteger('failed_login_attempts')->default(0);
            $table->timestamp('password_locked_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    public function test_third_failed_web_login_locks_account_and_shows_reset_link(): void
    {
        $user = User::create([
            'name' => 'Student User',
            'email' => 'student@example.test',
            'password' => Hash::make('correct-password'),
            'role' => 'student',
            'account_status' => 'active',
        ]);

        foreach (range(1, 2) as $attempt) {
            $this->from(route('login'))
                ->post(route('login'), [
                    'email' => $user->email,
                    'password' => 'wrong-password',
                ])
                ->assertRedirect(route('login'))
                ->assertSessionHasErrors('email');
        }

        $this->from(route('login'))
            ->post(route('login'), [
                'email' => $user->email,
                'password' => 'wrong-password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email')
            ->assertSessionHas('password_reset_url', route('password.code.form'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'failed_login_attempts' => 3,
        ]);
        $this->assertNotNull($user->fresh()->password_locked_at);
    }

    public function test_api_login_returns_reset_prompt_after_third_failed_attempt(): void
    {
        $user = User::create([
            'name' => 'Student User',
            'email' => 'student@example.test',
            'password' => Hash::make('correct-password'),
            'role' => 'student',
            'account_status' => 'active',
        ]);

        foreach (range(1, 2) as $attempt) {
            $this->postJson('/api/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ])->assertStatus(422);
        }

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertStatus(423)
            ->assertJsonPath('password_reset_url', route('password.code.form'));
    }
}
