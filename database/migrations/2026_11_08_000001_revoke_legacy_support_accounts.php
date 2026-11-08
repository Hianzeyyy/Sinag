<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->whereIn('email', ['admin@psu.edu.ph', 'student@psu.edu.ph'])
            ->update([
                'account_status' => 'rejected',
                'password' => Hash::make(Str::random(64)),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
    }
};
