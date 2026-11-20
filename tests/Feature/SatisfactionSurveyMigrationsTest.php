<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SatisfactionSurveyMigrationsTest extends TestCase
{
    public function test_duplicate_table_migration_does_not_block_item_specific_survey_columns(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });

        $baseMigration = require database_path('migrations/2026_11_12_000001_create_customer_satisfaction_surveys_table.php');
        $duplicateMigration = require database_path('migrations/2026_11_16_000002_create_satisfaction_surveys_table.php');
        $subjectMigration = require database_path('migrations/2026_11_18_000001_add_subject_to_customer_satisfaction_surveys.php');

        $baseMigration->up();
        $userId = DB::table('users')->insertGetId([
            'name' => 'Survey Test',
            'email' => 'survey@example.test',
            'password' => 'password',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('customer_satisfaction_surveys')->insert([
            'user_id' => $userId,
            'rating' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $duplicateMigration->up();
        $subjectMigration->up();

        $this->assertTrue(Schema::hasColumn('customer_satisfaction_surveys', 'subject'));
        $this->assertTrue(Schema::hasColumn('customer_satisfaction_surveys', 'subject_id'));
        $this->assertDatabaseCount('customer_satisfaction_surveys', 1);
        $this->assertDatabaseHas('customer_satisfaction_surveys', [
            'user_id' => $userId,
            'rating' => 5,
        ]);
    }
}
