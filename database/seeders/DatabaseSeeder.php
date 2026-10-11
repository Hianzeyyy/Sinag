<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Keep the known support accounts available without creating duplicates
        // when the seeder is run during a deployment.
        User::updateOrCreate(['email' => 'admin@psu.edu.ph'], [
            'name' => 'GAD Admin',
            'password' => Hash::make('password123'), // Ang iyong password
            'role' => 'admin',
            'cloak_alias' => 'AdminCloak',
            'account_status' => 'active', // Dapat active para sa enum requirement
        ]);

        $student = User::updateOrCreate(['email' => 'student@psu.edu.ph'], [
            'name' => 'Test Student',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'cloak_alias' => 'StudentCloak',
            'account_status' => 'active',
        ]);

        \App\Models\Suggestion::firstOrCreate([
            'user_id' => $student->id,
            'category' => 'Campus Safety',
            'message' => 'Sana dagdagan pa ang ilaw sa main gate para mas safe umuwi ang mga estudyante sa gabi.',
        ], [
            'is_anonymous' => true,
            'status' => 'New',
        ]);
    }
}