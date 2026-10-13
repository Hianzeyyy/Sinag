<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('suggestions', 'image_path')) {
            Schema::table('suggestions', function (Blueprint $table) {
                $table->string('image_path')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('suggestions', 'image_path')) {
            Schema::table('suggestions', function (Blueprint $table) {
                $table->dropColumn('image_path');
            });
        }
    }
};