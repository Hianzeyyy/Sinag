<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('users')->updateOrInsert(
            ['email' => 'admin@psu.edu.ph'],
            [
                'name' => 'GAD Admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'account_type' => 'admin',
                'cloak_alias' => 'AdminCloak',
                'account_status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'student@psu.edu.ph'],
            [
                'name' => 'Test Student',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'account_type' => 'student',
                'cloak_alias' => 'StudentCloak',
                'account_status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }

    public function down(): void
    {
        DB::table('users')
            ->whereIn('email', ['admin@psu.edu.ph', 'student@psu.edu.ph'])
            ->delete();
    }
};
