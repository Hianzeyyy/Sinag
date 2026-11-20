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

        $messages = [
            [2, 4, 2, 'hello admin', 'student', '2026-09-07 20:50:40', '2026-08-23 20:18:42', '2026-09-07 20:50:40'],
            [3, 11, 2, 'Hello GAD admin', 'student', '2026-09-09 00:55:58', '2026-09-08 01:27:35', '2026-09-09 00:55:58'],
            [4, 20, 2, 'hello admin', 'student', null, '2026-09-22 21:24:03', '2026-09-22 21:24:03'],
            [5, 4, 2, 'hello po', 'student', '2026-10-05 23:58:42', '2026-10-05 23:45:34', '2026-10-05 23:58:42'],
            [6, 4, 2, 'i have concern today', 'student', '2026-10-05 23:58:42', '2026-10-05 23:45:43', '2026-10-05 23:58:42'],
        ];

        foreach ($messages as [$id, $senderId, $receiverId, $message, $senderType, $readAt, $createdAt, $updatedAt]) {
            if (!DB::table('users')->where('id', $senderId)->exists()) {
                continue;
            }
            if (!DB::table('users')->where('id', $receiverId)->exists()) {
                continue;
            }

            DB::table('messages')->updateOrInsert(
                ['id' => $id],
                [
                    'sender_id' => $senderId,
                    'receiver_id' => $receiverId,
                    'message' => $message,
                    'sender_type' => $senderType,
                    'read_at' => $readAt,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]
            );
        }
    }

    public function down(): void
    {
        DB::table('messages')->whereIn('id', [2, 3, 4, 5, 6])->delete();
    }
};
