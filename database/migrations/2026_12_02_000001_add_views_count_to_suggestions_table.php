<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('suggestions') && !Schema::hasColumn('suggestions', 'views_count')) {
            Schema::table('suggestions', function (Blueprint $table): void {
                $table->unsignedBigInteger('views_count')->default(0);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('suggestions') && Schema::hasColumn('suggestions', 'views_count')) {
            Schema::table('suggestions', function (Blueprint $table): void {
                $table->dropColumn('views_count');
            });
        }
    }
};
