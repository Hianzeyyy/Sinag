<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('customer_satisfaction_surveys')) {
            return;
        }

        Schema::table('customer_satisfaction_surveys', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_satisfaction_surveys', 'subject')) {
                $table->string('subject', 20)->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('customer_satisfaction_surveys', 'subject_id')) {
                $table->unsignedBigInteger('subject_id')->nullable()->after('subject');
            }
        });

        try {
            Schema::table('customer_satisfaction_surveys', function (Blueprint $table) {
                $table->dropUnique(['user_id']);
            });
        } catch (\Throwable $e) {
        }

        Schema::table('customer_satisfaction_surveys', function (Blueprint $table) {
            $table->index(['user_id', 'subject', 'subject_id'], 'survey_subject_lookup');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('customer_satisfaction_surveys')) {
            return;
        }

        try {
            Schema::table('customer_satisfaction_surveys', function (Blueprint $table) {
                $table->dropIndex('survey_subject_lookup');
                $table->dropColumn(['subject', 'subject_id']);
                $table->unique('user_id');
            });
        } catch (\Throwable $e) {
        }
    }
};
