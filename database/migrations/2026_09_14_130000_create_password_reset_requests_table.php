<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_reset_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reference_number')->unique();
            $table->string('email')->index();
            $table->string('name');
            $table->string('student_employee_id')->index();
            $table->string('department')->nullable();
            $table->string('id_picture_path');
            $table->text('reason')->nullable();
            $table->string('status')->default('pending')->index();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_remarks')->nullable();
            $table->string('reset_token_hash')->nullable();
            $table->timestamp('reset_token_expires_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_requests');
    }
};