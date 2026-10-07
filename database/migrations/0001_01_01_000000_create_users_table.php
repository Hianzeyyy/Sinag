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
        // 1. USERS TABLE - Basehan ng Authentication at Cloak Identity
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // Restricted sa @psu.edu.ph domain
            $table->string('email')->unique(); 
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // Roles para sa Access Control
            $table->enum('role', ['student', 'admin', 'security'])->default('student'); 
            
            // "The Cloak" Identity: System-alias para sa anonymity
            $table->string('cloak_alias')->nullable()->unique(); 
            
            $table->rememberToken();
            $table->timestamps();
        });

        // ...existing code...

        // ...existing code...

        // 4. SESSIONS TABLE - Fix para sa "Table sessions doesn't exist" error
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // 5. PASSWORD RESET TOKENS
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suggestions');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};