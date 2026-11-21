<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('reports', 'resolution_notice_sent_at')) {
            Schema::table('reports', function (Blueprint $table): void {
                $table->timestamp('resolution_notice_sent_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('reports', 'resolution_notice_sent_at')) {
            Schema::table('reports', function (Blueprint $table): void {
                $table->dropColumn('resolution_notice_sent_at');
            });
        }
    }
};
