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
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable()->after('name');
            }

            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable()->after('last_name');
            }

            if (!Schema::hasColumn('users', 'middle_name')) {
                $table->string('middle_name')->nullable()->after('first_name');
            }

            if (!Schema::hasColumn('users', 'age')) {
                $table->integer('age')->nullable()->after('email');
            }

            if (!Schema::hasColumn('users', 'gender')) {
                $table->string('gender')->nullable()->after('age');
            }

            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department')->nullable()->after('gender');
            }

            if (!Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number')->nullable()->after('department');
            }

            if (!Schema::hasColumn('users', 'account_type')) {
                $table->string('account_type')->nullable()->after('role');
            }

            if (!Schema::hasColumn('users', 'id_image_path')) {
                $table->string('id_image_path')->nullable()->after('cloak_alias');
            }

            if (!Schema::hasColumn('users', 'selfie_image_path')) {
                $table->string('selfie_image_path')->nullable()->after('id_image_path');
            }

            if (!Schema::hasColumn('users', 'profile_photo_path')) {
                $table->string('profile_photo_path')->nullable()->after('selfie_image_path');
            }

            if (!Schema::hasColumn('users', 'account_status')) {
                $table->string('account_status')->default('active')->after('profile_photo_path');
            }

            if (!Schema::hasColumn('users', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('account_status');
            }

            if (!Schema::hasColumn('users', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
            }

            if (!Schema::hasColumn('users', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('approved_by');
            }

            if (!Schema::hasColumn('users', 'education')) {
                $table->json('education')->nullable()->after('rejection_reason');
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
                'last_name',
                'first_name',
                'middle_name',
                'age',
                'gender',
                'department',
                'phone_number',
                'account_type',
                'id_image_path',
                'selfie_image_path',
                'profile_photo_path',
                'account_status',
                'approved_at',
                'approved_by',
                'rejection_reason',
                'education',
            ];

            $existingColumns = array_values(array_filter($columns, fn ($column) => Schema::hasColumn('users', $column)));

            if (!empty($existingColumns)) {
                $table->dropColumn($existingColumns);
            }
        });
    }
};