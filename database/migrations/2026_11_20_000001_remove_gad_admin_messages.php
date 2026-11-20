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
            ->where(function ($query) {
                $query->whereNull('account_type')
                    ->orWhereRaw('LOWER(TRIM(COALESCE(account_type, ?))) != ?', ['', 'coordinator']);
            })
            ->whereRaw('LOWER(TRIM(COALESCE(email, ?))) != ?', ['', 'gadcoordinator@psu.edu.ph'])
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if ($gadAdminIds === []) {
            return;
        }

        DB::table('messages')
            ->where(function ($query) use ($gadAdminIds) {
                $query->whereIn('sender_id', $gadAdminIds)
                    ->orWhereIn('receiver_id', $gadAdminIds);
            })
            ->delete();
    }

    public function down(): void
    {
        // Deleted message records cannot be restored safely.
    }
};
