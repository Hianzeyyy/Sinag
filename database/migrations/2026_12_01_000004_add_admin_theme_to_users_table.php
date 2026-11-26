<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'admin_theme')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('admin_theme', 10)->default('light');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'admin_theme')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('admin_theme');
            });
        }
    }
};
