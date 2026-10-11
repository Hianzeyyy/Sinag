<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            if (!Schema::hasColumn('reports', 'course')) {
                $table->string('course', 120)->nullable()->after('nature');
            }

            if (!Schema::hasColumn('reports', 'gender')) {
                $table->string('gender', 30)->nullable()->after('course');
            }

            if (!Schema::hasColumn('reports', 'incident_time')) {
                $table->time('incident_time')->nullable()->after('incident_date');
            }

            if (!Schema::hasColumn('reports', 'seen_at')) {
                $table->dateTime('seen_at')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            foreach (['course', 'gender', 'incident_time', 'seen_at'] as $column) {
                if (Schema::hasColumn('reports', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
