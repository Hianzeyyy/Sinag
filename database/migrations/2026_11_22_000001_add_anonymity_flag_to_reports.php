<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('reports') && ! Schema::hasColumn('reports', 'is_anonymous')) {
            Schema::table('reports', function (Blueprint $table): void {
                $table->boolean('is_anonymous')->default(true);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('reports') && Schema::hasColumn('reports', 'is_anonymous')) {
            Schema::table('reports', function (Blueprint $table): void {
                $table->dropColumn('is_anonymous');
            });
        }
    }
};
