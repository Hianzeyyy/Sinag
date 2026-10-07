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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            // Iniuugnay ang report sa user (student) na nag-submit
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Core Report Details
            $table->string('incident_id')->unique(); // Halimbawa: SN-1024-ABC
            $table->string('cloak_alias');           // Ang anonymous name
            $table->string('nature');               // Catcalling, Harassment, etc.
            
            $table->date('incident_date')->nullable(); // Ginawang nullable() para sa records na walang incident_date
            $table->string('location');             // Saan nangyari
            $table->text('description')->nullable(); // Detalye
            
            // Management Fields (Importante para sa Dashboard Analytics)
            $table->string('priority')->default('Medium'); // Low, Medium, High
            $table->string('status')->default('Pending');  // Pending, Emergency, Resolved
            
            // File Upload Support
            $table->json('evidence')->nullable(); // Para sa mga pictures/files na isusumite
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};