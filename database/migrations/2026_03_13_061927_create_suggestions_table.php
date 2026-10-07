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
        Schema::create('suggestions', function (Blueprint $table) {
            $table->id();
            // Iniuugnay ang suggestion sa estudyante
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Suggestion Details
            $table->string('category');       // Lighting, Security, Facilities, etc.
            // $table->string('subject');        // Removed: No longer used
            $table->text('message');          // Ang mismong detalye ng suggestion
            
            // Settings
            $table->boolean('is_anonymous')->default(true); // Kung gusto ba nilang itago ang pangalan nila
            $table->string('status')->default('New');       // New, Reviewed, Action Taken
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suggestions');
    }
};