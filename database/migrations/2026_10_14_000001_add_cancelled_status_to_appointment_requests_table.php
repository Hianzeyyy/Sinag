<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE appointment_requests MODIFY status ENUM('pending', 'completed', 'approved', 'rejected', 'cancelled') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::table('appointment_requests')->where('status', 'cancelled')->update(['status' => 'rejected']);

        DB::statement("ALTER TABLE appointment_requests MODIFY status ENUM('pending', 'completed', 'approved', 'rejected') NOT NULL DEFAULT 'pending'");
    }
};
