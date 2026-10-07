<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'personal_information')) {
                $table->json('personal_information')->nullable()->after('education');
            }

            if (!Schema::hasColumn('users', 'family_background')) {
                $table->json('family_background')->nullable()->after('personal_information');
            }

            if (!Schema::hasColumn('users', 'student_information')) {
                $table->json('student_information')->nullable()->after('family_background');
            }

            if (!Schema::hasColumn('users', 'employee_education')) {
                $table->json('employee_education')->nullable()->after('student_information');
            }

            if (!Schema::hasColumn('users', 'civil_service_eligibility')) {
                $table->json('civil_service_eligibility')->nullable()->after('employee_education');
            }

            if (!Schema::hasColumn('users', 'work_experience')) {
                $table->json('work_experience')->nullable()->after('civil_service_eligibility');
            }

            if (!Schema::hasColumn('users', 'voluntary_work')) {
                $table->json('voluntary_work')->nullable()->after('work_experience');
            }

            if (!Schema::hasColumn('users', 'gad_training')) {
                $table->json('gad_training')->nullable()->after('voluntary_work');
            }

            if (!Schema::hasColumn('users', 'other_information')) {
                $table->json('other_information')->nullable()->after('gad_training');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'personal_information',
                'family_background',
                'student_information',
                'employee_education',
                'civil_service_eligibility',
                'work_experience',
                'voluntary_work',
                'gad_training',
                'other_information',
            ];

            $existingColumns = array_values(array_filter($columns, fn ($column) => Schema::hasColumn('users', $column)));

            if (!empty($existingColumns)) {
                $table->dropColumn($existingColumns);
            }
        });
    }
};