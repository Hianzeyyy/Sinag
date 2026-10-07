<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('gad_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('location');
            $table->dateTime('available_from');
            $table->dateTime('available_until')->nullable();
            $table->text('details')->nullable();
            $table->string('status')->default('active');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gad_schedules');
    }
};