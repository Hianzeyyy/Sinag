<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE appointment_requests DROP CONSTRAINT IF EXISTS appointment_requests_status_check');
            DB::statement("ALTER TABLE appointment_requests ADD CONSTRAINT appointment_requests_status_check CHECK (status IN ('pending', 'completed', 'approved', 'rejected'))");

            return;
        }

        if ($driver === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE appointment_requests MODIFY status ENUM('pending', 'completed', 'approved', 'rejected') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE appointment_requests DROP CONSTRAINT IF EXISTS appointment_requests_status_check');
            DB::statement("ALTER TABLE appointment_requests ADD CONSTRAINT appointment_requests_status_check CHECK (status IN ('pending', 'approved', 'rejected'))");

            return;
        }

        if ($driver === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE appointment_requests MODIFY status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending'");
    }
};