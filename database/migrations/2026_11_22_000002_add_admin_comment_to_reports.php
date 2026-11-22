<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('reports') && ! Schema::hasColumn('reports', 'admin_comment')) {
            Schema::table('reports', function (Blueprint $table): void {
                $table->text('admin_comment')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('reports') && Schema::hasColumn('reports', 'admin_comment')) {
            Schema::table('reports', function (Blueprint $table): void {
                $table->dropColumn('admin_comment');
            });
        }
    }
};
