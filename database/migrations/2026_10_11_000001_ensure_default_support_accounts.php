<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $upsertUser = function (array $attributes, array $values) use ($now): void {
            $existingId = DB::table('users')
                ->where('email', $attributes['email'])
                ->value('id');

            if ($existingId) {
                DB::table('users')->where('id', $existingId)->update($values + ['updated_at' => $now]);

                return;
            }

            DB::table('users')->insert($values + [
                'id' => ((int) DB::table('users')->max('id')) + 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        };

        $upsertUser(['email' => 'admin@psu.edu.ph'], [
                'name' => 'GAD Admin',
                'email' => 'admin@psu.edu.ph',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'account_type' => 'admin',
                'cloak_alias' => 'AdminCloak',
                'account_status' => 'active',
        ]);

        $upsertUser(['email' => 'student@psu.edu.ph'], [
                'name' => 'Test Student',
                'email' => 'student@psu.edu.ph',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'account_type' => 'student',
                'cloak_alias' => 'StudentCloak',
                'account_status' => 'active',
        ]);
    }

    public function down(): void
    {
        DB::table('users')
            ->whereIn('email', ['admin@psu.edu.ph', 'student@psu.edu.ph'])
            ->delete();
    }
};
