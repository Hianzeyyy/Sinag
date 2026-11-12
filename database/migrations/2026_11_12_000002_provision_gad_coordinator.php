<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $email = 'gadcoordinator@psu.edu.ph';
        $attributes = [
            'name' => 'GAD Coordinator',
            'email' => $email,
            'password' => Hash::make('gadcoordinator_123'),
            'role' => 'admin',
            'account_type' => 'coordinator',
            'account_status' => 'active',
            'approved_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (DB::table('users')->where('email', $email)->exists()) {
            DB::table('users')->where('email', $email)->update([
                'account_type' => 'coordinator',
                'role' => 'admin',
                'account_status' => 'active',
                'approved_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            DB::table('users')->insert($attributes);
        }
    }

    public function down(): void
    {
        DB::table('users')->where('email', 'gadcoordinator@psu.edu.ph')->delete();
    }
};
