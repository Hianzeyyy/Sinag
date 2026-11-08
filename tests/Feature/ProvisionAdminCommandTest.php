<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProvisionAdminCommandTest extends TestCase
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
            $table->string('account_type')->default('student');
            $table->string('account_status')->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    public function test_it_creates_an_admin_with_the_operator_chosen_password(): void
    {
        $password = 'SecureAdmin!2026';

        $this->artisan('app:provision-admin', ['email' => 'admin@example.test'])
            ->expectsQuestion('Administrator name', 'GAD Admin')
            ->expectsQuestion('Administrator password (12+ characters, mixed case, number, and symbol)', $password)
            ->expectsQuestion('Confirm administrator password', $password)
            ->expectsOutputToContain('Administrator account admin@example.test is ready.')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.test',
            'role' => 'admin',
            'account_type' => 'admin',
            'account_status' => 'active',
        ]);

        $this->assertTrue(Hash::check($password, \App\Models\User::where('email', 'admin@example.test')->value('password')));
    }

    public function test_it_reactivates_a_rejected_admin_with_a_new_operator_chosen_password(): void
    {
        $user = \App\Models\User::create([
            'name' => 'Legacy Admin',
            'email' => 'admin@example.test',
            'password' => Hash::make('LegacyPassword!2026'),
            'role' => 'student',
            'account_type' => 'student',
            'account_status' => 'rejected',
            'rejection_reason' => 'Legacy account disabled.',
        ]);
        $newPassword = 'ReactivatedAdmin!2026';

        $this->artisan('app:provision-admin', ['email' => $user->email])
            ->expectsConfirmation('Promote admin@example.test to administrator and replace its password?', 'yes')
            ->expectsQuestion('Administrator password (12+ characters, mixed case, number, and symbol)', $newPassword)
            ->expectsQuestion('Confirm administrator password', $newPassword)
            ->expectsOutputToContain('Administrator account admin@example.test is ready.')
            ->assertExitCode(0);

        $user->refresh();
        $this->assertSame('admin', $user->role);
        $this->assertSame('admin', $user->account_type);
        $this->assertSame('active', $user->account_status);
        $this->assertNull($user->rejection_reason);
        $this->assertTrue(Hash::check($newPassword, $user->password));
        $this->assertFalse(Hash::check('LegacyPassword!2026', $user->password));
    }

    public function test_it_can_provision_an_admin_without_interactive_prompts(): void
    {
        $password = 'SecureDeploymentAdmin!2026';

        $this->artisan('app:provision-admin', [
            'email' => 'admin@example.test',
            '--name' => 'Deployment Admin',
            '--password' => $password,
            '--force' => true,
        ])
            ->expectsOutputToContain('Administrator account admin@example.test is ready.')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.test',
            'name' => 'Deployment Admin',
            'role' => 'admin',
            'account_type' => 'admin',
            'account_status' => 'active',
        ]);
        $this->assertTrue(Hash::check($password, \App\Models\User::where('email', 'admin@example.test')->value('password')));
    }

    public function test_it_revokes_the_legacy_support_accounts(): void
    {
        foreach (['admin@psu.edu.ph', 'student@psu.edu.ph'] as $email) {
            \App\Models\User::create([
                'name' => 'Legacy Account',
                'email' => $email,
                'password' => 'legacy-password',
                'role' => 'admin',
                'account_type' => 'admin',
                'account_status' => 'active',
            ]);
        }

        \App\Models\User::create([
            'name' => 'Unrelated Account',
            'email' => 'other@example.test',
            'password' => 'other-password',
            'role' => 'student',
            'account_type' => 'student',
            'account_status' => 'active',
        ]);

        $migration = require database_path('migrations/2026_11_08_000001_revoke_legacy_support_accounts.php');
        $migration->up();

        $this->assertDatabaseHas('users', ['email' => 'admin@psu.edu.ph', 'account_status' => 'rejected']);
        $this->assertDatabaseHas('users', ['email' => 'student@psu.edu.ph', 'account_status' => 'rejected']);
        $this->assertDatabaseHas('users', ['email' => 'other@example.test', 'account_status' => 'active']);
        $this->assertFalse(Hash::check('legacy-password', \App\Models\User::where('email', 'admin@psu.edu.ph')->value('password')));
    }
}
