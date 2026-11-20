<?php

namespace Tests\Feature;

use App\Mail\AccountStatusMail;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminAccountNotificationTest extends TestCase
{
    public function test_account_status_email_templates_have_the_correct_content(): void
    {
        $approvedHtml = (new AccountStatusMail('Student User', 'approved', 'Welcome to SINAG'))->render();
        $rejectedHtml = (new AccountStatusMail('Student User', 'rejected', 'Verification failed'))->render();

        $this->assertStringContainsString('verified and approved by the GAD administrator', $approvedHtml);
        $this->assertStringContainsString('Welcome to SINAG', $approvedHtml);
        $this->assertStringContainsString(route('login'), $approvedHtml);
        $this->assertStringContainsString('registration was declined by the administrator', $rejectedHtml);
        $this->assertStringContainsString('Verification failed', $rejectedHtml);
        $this->assertStringContainsString('PSU Asingan GAD office', $rejectedHtml);
    }

    public function test_approving_an_account_emails_the_institutional_address(): void
    {
        $this->createAccountSchema();
        $admin = $this->createUser('GAD Admin', 'admin@example.test', 'admin', 'active');
        $user = $this->createUser('Student User', 'student@example.test', 'student', 'pending');
        config([
            'mail.from.address' => 'sinag-mailer@example.test',
            'mail.from.name' => 'SINAG Mailer',
        ]);

        Mail::fake();

        $this->actingAs($admin)
            ->post(route('admin.users.approve', $user->id), ['approval_note' => 'Welcome to SINAG'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'account_status' => 'active']);
        Mail::assertSent(AccountStatusMail::class, fn (AccountStatusMail $mail): bool =>
            $mail->hasTo($user->email)
            && $mail->userName === $user->name
            && $mail->status === 'approved'
            && $mail->remarks === 'Welcome to SINAG'
            && $mail->envelope()->subject === 'SINAG Account Approved – Pangasinan State University'
        );
    }

    public function test_approval_succeeds_when_optional_audit_columns_are_missing(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role');
            $table->string('account_status')->nullable();
            $table->timestamps();
        });

        $admin = $this->createUser('GAD Admin', 'admin@example.test', 'admin', 'active');
        $user = $this->createUser('Student User', 'student@example.test', 'student', 'pending');
        Mail::fake();

        $this->actingAs($admin)
            ->post(route('admin.users.approve', $user->id))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'account_status' => 'active']);
        Mail::assertSent(AccountStatusMail::class, fn (AccountStatusMail $mail): bool =>
            $mail->hasTo($user->email) && $mail->status === 'approved'
        );
    }

    public function test_rejecting_an_account_emails_the_institutional_address(): void
    {
        $this->createAccountSchema();
        $admin = $this->createUser('GAD Admin', 'admin@example.test', 'admin', 'active');
        $user = $this->createUser('Student User', 'student@example.test', 'student', 'pending');
        config([
            'mail.from.address' => 'sinag-mailer@example.test',
            'mail.from.name' => 'SINAG Mailer',
        ]);

        Mail::fake();

        $this->actingAs($admin)
            ->post(route('admin.users.reject', $user->id), ['rejection_reason' => 'Verification failed'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'account_status' => 'rejected']);
        Mail::assertSent(AccountStatusMail::class, fn (AccountStatusMail $mail): bool =>
            $mail->hasTo($user->email)
            && $mail->userName === $user->name
            && $mail->status === 'rejected'
            && $mail->remarks === 'Verification failed'
            && $mail->envelope()->subject === 'SINAG Account Registration Update – Pangasinan State University'
        );
    }

    public function test_email_failure_does_not_reverse_approval_and_warns_the_admin(): void
    {
        $this->createAccountSchema();
        $admin = $this->createUser('GAD Admin', 'admin@example.test', 'admin', 'active');
        $user = $this->createUser('Student User', 'student@example.test', 'student', 'pending');
        $mailer = \Mockery::mock();
        $mailer->shouldReceive('send')
            ->once()
            ->with(\Mockery::type(AccountStatusMail::class))
            ->andThrow(new \RuntimeException('Resend delivery failed'));
        Mail::shouldReceive('to')->once()->with($user->email)->andReturn($mailer);
        Log::spy();

        $this->actingAs($admin)
            ->post(route('admin.users.approve', $user->id))
            ->assertSessionHas('warning', 'Account approved, but the notification email could not be sent. Check the mail configuration and contact the user.');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'account_status' => 'active']);
        Log::shouldHaveReceived('error')->once()->with(
            'SINAG account approval email failed',
            \Mockery::on(fn (array $context): bool => $context['user_id'] === $user->id
                && $context['exception'] === \RuntimeException::class)
        );
    }

    public function test_email_failure_does_not_reverse_rejection_and_warns_the_admin(): void
    {
        $this->createAccountSchema();
        $admin = $this->createUser('GAD Admin', 'admin@example.test', 'admin', 'active');
        $user = $this->createUser('Student User', 'student@example.test', 'student', 'pending');
        $mailer = \Mockery::mock();
        $mailer->shouldReceive('send')
            ->once()
            ->with(\Mockery::type(AccountStatusMail::class))
            ->andThrow(new \RuntimeException('Resend delivery failed'));
        Mail::shouldReceive('to')->once()->with($user->email)->andReturn($mailer);
        Log::spy();

        $this->actingAs($admin)
            ->post(route('admin.users.reject', $user->id), ['rejection_reason' => 'Verification failed'])
            ->assertSessionHas('warning', 'Account rejected, but the notification email could not be sent. Check the mail configuration and contact the user.');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'account_status' => 'rejected',
            'rejection_reason' => 'Verification failed',
        ]);
        Log::shouldHaveReceived('error')->once()->with(
            'SINAG account rejection email failed',
            \Mockery::on(fn (array $context): bool => $context['user_id'] === $user->id
                && $context['exception'] === \RuntimeException::class)
        );
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
