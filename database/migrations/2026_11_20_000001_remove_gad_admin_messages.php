<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('messages') || !Schema::hasTable('users')) {
            return;
        }

        $gadAdminIds = DB::table('users')
            ->whereRaw('LOWER(TRIM(COALESCE(role, ?))) = ?', ['', 'admin'])
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if ($gadAdminIds === []) {
            return;
        }

        DB::table('messages')
            ->where(function ($query) use ($gadAdminIds) {
                $query->whereIn('sender_id', $gadAdminIds)
                    ->whereIn('receiver_id', $gadAdminIds);
            })
            ->delete();
    }

    public function down(): void
    {
        // Deleted message records cannot be restored safely.
    }
};
