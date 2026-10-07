<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->nullable()->after('email');
            $table->string('id_image_path')->nullable()->after('cloak_alias');
            $table->string('selfie_image_path')->nullable()->after('id_image_path');
            $table->string('account_status')->default('pending')->after('selfie_image_path');
            $table->timestamp('approved_at')->nullable()->after('account_status');
            $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
            $table->text('rejection_reason')->nullable()->after('approved_by');
        });

        // Keep existing accounts usable; only new registrations will require approval.
        DB::table('users')->update([
            'account_status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone_number',
                'id_image_path',
                'selfie_image_path',
                'account_status',
                'approved_at',
                'approved_by',
                'rejection_reason',
            ]);
        });
    }
};
