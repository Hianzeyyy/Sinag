<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        $email = strtolower(trim((string) env('USER_EMAIL_TO_DELETE', '')));
        if ($email === '') {
            if (app()->environment('production')) {
                throw new RuntimeException('Set USER_EMAIL_TO_DELETE before running the configured user deletion migration.');
            }

            return;
        }

        $columns = ['id', 'role'];
        foreach (['profile_photo_path', 'id_image_path', 'selfie_image_path'] as $mediaColumn) {
            if (Schema::hasColumn('users', $mediaColumn)) {
                $columns[] = $mediaColumn;
            }
        }

        $user = DB::table('users')
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first($columns);

        if (! $user) {
            throw new RuntimeException('No account matches USER_EMAIL_TO_DELETE.');
        }

        if (strtolower(trim((string) $user->role)) === 'admin') {
            throw new RuntimeException('The configured deletion target is an administrator; refusing to delete it.');
        }

        $mediaPaths = array_filter([
            $user->profile_photo_path ?? null,
            $user->id_image_path ?? null,
            $user->selfie_image_path ?? null,
        ]);

        if (Schema::hasTable('password_reset_requests') && Schema::hasColumn('password_reset_requests', 'id_picture_path')) {
            $mediaPaths = array_merge($mediaPaths, DB::table('password_reset_requests')
                ->where('user_id', $user->id)
                ->whereNotNull('id_picture_path')
                ->pluck('id_picture_path')
                ->all());
        }

        if (Schema::hasTable('suggestions') && Schema::hasColumn('suggestions', 'image_path')) {
            $mediaPaths = array_merge($mediaPaths, DB::table('suggestions')
                ->where('user_id', $user->id)
                ->whereNotNull('image_path')
                ->pluck('image_path')
                ->all());
        }

        if (Schema::hasTable('reports') && Schema::hasColumn('reports', 'evidence')) {
            foreach (DB::table('reports')->where('user_id', $user->id)->whereNotNull('evidence')->pluck('evidence') as $evidence) {
                $decoded = is_string($evidence) ? json_decode($evidence, true) : null;
                foreach (is_array($decoded) ? $decoded : [$evidence] as $path) {
                    if (is_string($path) && filled($path)) {
                        $mediaPaths[] = $path;
                    }
                }
            }
        }

        foreach (array_unique($mediaPaths) as $path) {
            foreach (array_unique([
                'public',
                'local',
                config('filesystems.user_public_disk'),
                config('filesystems.user_private_disk'),
            ]) as $diskName) {
                Storage::disk($diskName)->delete($path);
            }
        }

        DB::transaction(function () use ($user): void {
            if (Schema::hasTable('personal_access_tokens')) {
                DB::table('personal_access_tokens')
                    ->where('tokenable_id', $user->id)
                    ->where('tokenable_type', User::class)
                    ->delete();
            }

            $sessionTable = config('session.table', 'sessions');
            if (Schema::hasTable($sessionTable) && Schema::hasColumn($sessionTable, 'user_id')) {
                DB::table($sessionTable)->where('user_id', $user->id)->delete();
            }

            if (Schema::hasTable('password_reset_requests')) {
                DB::table('password_reset_requests')->where('user_id', $user->id)->delete();
            }

            DB::table('users')->where('id', $user->id)->delete();
        });
    }

    public function down(): void
    {
        // Account and related records cannot be restored by rolling back this migration.
    }
};
