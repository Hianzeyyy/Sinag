<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('messages', 'original_message')) {
            Schema::table('messages', function (Blueprint $table): void {
                $table->text('original_message')->nullable();
            });
        }

        if (! Schema::hasColumn('messages', 'deleted_at')) {
            Schema::table('messages', function (Blueprint $table): void {
                $table->timestamp('deleted_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        $columns = array_values(array_filter(
            ['original_message', 'deleted_at'],
            fn (string $column): bool => Schema::hasColumn('messages', $column)
        ));

        if ($columns) {
            Schema::table('messages', function (Blueprint $table) use ($columns): void {
                $table->dropColumn($columns);
            });
        }
    }
};
