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
            if (!Schema::hasColumn('users', 'account_status')) {
                $table->enum('account_status', ['pending', 'active', 'rejected'])->default('pending')->after('account_type');
            }
            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department')->nullable()->after('account_status');
            }
            if (!Schema::hasColumn('users', 'age')) {
                $table->integer('age')->nullable()->after('department');
            }
            if (!Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number')->nullable()->after('age');
            }
            if (!Schema::hasColumn('users', 'profile_photo_path')) {
                $table->string('profile_photo_path')->nullable()->after('phone_number');
            }
            if (!Schema::hasColumn('users', 'education')) {
                $table->json('education')->nullable()->after('profile_photo_path');
            }
            if (!Schema::hasColumn('users', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('education');
            }
            if (!Schema::hasColumn('users', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
            }
            if (!Schema::hasColumn('users', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('approved_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_name',
                'first_name',
                'middle_name',
                'account_type',
                'account_status',
                'department',
                'age',
                'phone_number',
                'profile_photo_path',
                'education',
                'approved_at',
                'approved_by',
                'rejection_reason'
            ]);
        });
    }
};
