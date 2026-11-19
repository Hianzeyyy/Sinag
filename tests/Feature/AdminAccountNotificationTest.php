<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminAccountNotificationTest extends TestCase
{
    public function test_approving_an_account_emails_the_institutional_address(): void
    {
        $this->createAccountSchema();
        $admin = $this->createUser('GAD Admin', 'admin@example.test', 'admin', 'active');
        $user = $this->createUser('Student User', 'student@example.test', 'student', 'pending');

        Mail::shouldReceive('raw')
            ->once()
            ->withArgs(function ($content, $configureMessage) use ($user): bool {
                $this->assertStringContainsString('Your SINAG account has been approved', $content);
                $this->assertIsCallable($configureMessage);

                $message = \Mockery::mock();
                $message->shouldReceive('to')->once()->with($user->email)->andReturnSelf();
                $message->shouldReceive('subject')->once()->with('SINAG Account Approved');
                $configureMessage($message);

                return true;
            });

        $this->actingAs($admin)
            ->post(route('admin.users.approve', $user->id))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'account_status' => 'active']);
    }

    public function test_rejecting_an_account_emails_the_institutional_address(): void
    {
        $this->createAccountSchema();
        $admin = $this->createUser('GAD Admin', 'admin@example.test', 'admin', 'active');
        $user = $this->createUser('Student User', 'student@example.test', 'student', 'pending');

        Mail::shouldReceive('raw')
            ->once()
            ->withArgs(function ($content, $configureMessage) use ($user): bool {
                $this->assertStringContainsString('Your SINAG account has been rejected', $content);
                $this->assertIsCallable($configureMessage);

                $message = \Mockery::mock();
                $message->shouldReceive('to')->once()->with($user->email)->andReturnSelf();
                $message->shouldReceive('subject')->once()->with('SINAG Account Rejected');
                $configureMessage($message);

                return true;
            });

        $this->actingAs($admin)
            ->post(route('admin.users.reject', $user->id), ['rejection_reason' => 'Verification failed'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'account_status' => 'rejected']);
    }

    private function createAccountSchema(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role');
            $table->string('account_status')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('personal_access_tokens', function (Blueprint $table): void {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    private function createUser(string $name, string $email, string $role, string $status): User
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => 'password',
            'role' => $role,
            'account_status' => $status,
        ]);
    }
}
