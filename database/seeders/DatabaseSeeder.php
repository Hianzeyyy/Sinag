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
        // Gumawa ng Default Admin Account
        User::create([
            'name' => 'GAD Admin',
            'email' => 'admin@psu.edu.ph', // Gamitin ito sa login screen
            'password' => Hash::make('password123'), // Ang iyong password
            'role' => 'admin',
            'cloak_alias' => 'AdminCloak',
            'account_status' => 'active', // Dapat active para sa enum requirement
        ]);

        // Optional: Gumawa rin ng Test Student account para sa testing
        User::create([
            'name' => 'Test Student',
            'email' => 'student@psu.edu.ph',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'cloak_alias' => 'StudentCloak',
        ]);
        // Maglagay ng sample suggestion para sa Participatory Suggestions
        \App\Models\Suggestion::create([
            'user_id' => 2, // Test Student
            'category' => 'Campus Safety',
            // 'subject' => 'Mas maliwanag na ilaw sa gate', // Removed: No longer used
            'message' => 'Sana dagdagan pa ang ilaw sa main gate para mas safe umuwi ang mga estudyante sa gabi.',
            'is_anonymous' => true,
            'status' => 'New',
        ]);
    }
}