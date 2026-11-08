<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Support\CompatibleInsert;

class ProvisionAdmin extends Command
{
    protected $signature = 'app:provision-admin {email : Email address for the administrator account}';

    protected $description = 'Create or securely promote an administrator account';

    public function handle(): int
    {
        $email = strtolower(trim((string) $this->argument('email')));
        $emailValidator = Validator::make(['email' => $email], [
            'email' => ['required', 'email', 'max:255'],
        ]);

        if ($emailValidator->fails()) {
            $this->error($emailValidator->errors()->first('email'));

            return self::FAILURE;
        }

        $user = User::where('email', $email)->first();

        if ($user && !$this->confirm("Promote {$email} to administrator and replace its password?")) {
            $this->info('No changes were made.');

            return self::SUCCESS;
        }

        $name = $user?->name ?? trim((string) $this->ask('Administrator name'));
        $nameValidator = Validator::make(['name' => $name], [
            'name' => ['required', 'string', 'max:255'],
        ]);

        if ($nameValidator->fails()) {
            $this->error($nameValidator->errors()->first('name'));

            return self::FAILURE;
        }

        $password = $this->secret('Administrator password (12+ characters, mixed case, number, and symbol)');
        $passwordConfirmation = $this->secret('Confirm administrator password');

        if (!is_string($password) || $password === '' || $password !== $passwordConfirmation) {
            $this->error('The password is empty or the confirmation does not match.');

            return self::FAILURE;
        }

        $passwordValidator = Validator::make(['password' => $password], [
            'password' => [
                'required',
                Password::min(12)->letters()->mixedCase()->numbers()->symbols(),
            ],
        ]);

        if ($passwordValidator->fails()) {
            $this->error($passwordValidator->errors()->first('password'));

            return self::FAILURE;
        }

        $attributes = [
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'account_type' => 'admin',
            'account_status' => 'active',
            'approved_at' => now(),
            'rejection_reason' => null,
        ];

        if ($user) {
            $user->fill($attributes)->save();
        } else {
            CompatibleInsert::insert('users', $attributes);
        }

        $this->info("Administrator account {$email} is ready.");

        return self::SUCCESS;
    }
}
