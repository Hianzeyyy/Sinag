<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('messages') && ! Schema::hasColumn('messages', 'is_anonymous')) {
            Schema::table('messages', function (Blueprint $table): void {
                $table->boolean('is_anonymous')->default(false);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('messages') && Schema::hasColumn('messages', 'is_anonymous')) {
            Schema::table('messages', function (Blueprint $table): void {
                $table->dropColumn('is_anonymous');
            });
        }
    }
};
