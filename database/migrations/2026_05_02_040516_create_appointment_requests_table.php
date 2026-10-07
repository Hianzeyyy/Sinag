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
        Schema::create('appointment_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('full_name');
            $table->string('student_id')->nullable();
            $table->string('purpose_of_visit');
            $table->enum('urgency_level', ['Low', 'Medium', 'High']);
            $table->date('scheduled_date');
            $table->time('scheduled_time');
            $table->longText('description');
            $table->enum('format', ['Face-to-Face', 'Online']);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->longText('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_requests');
    }
};
