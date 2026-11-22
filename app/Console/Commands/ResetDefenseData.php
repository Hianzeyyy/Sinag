<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetDefenseData extends Command
{
    protected $signature = 'sinag:reset-defense-data {--force : Execute the cleanup instead of showing a dry run}';

    protected $description = 'Remove test users and transactional records while preserving official admin accounts';

    private const PROTECTED_EMAILS = [
        '29as2090_ms@psu.edu.ph',
        '22ur1099@psu.edu.ph',
    ];

    public function handle(): int
    {
        $protectedEmails = array_map('strtolower', self::PROTECTED_EMAILS);

        $keptUserIds = User::query()
            ->get(['id', 'email', 'role', 'account_type'])
            ->filter(function (User $user) use ($protectedEmails): bool {
                $email = strtolower(trim((string) $user->email));
                $role = strtolower(trim((string) $user->role));
                $accountType = strtolower(trim((string) $user->account_type));

                return in_array($email, $protectedEmails, true)
                    || $role === 'admin'
                    || $accountType === 'coordinator';
            })
            ->pluck('id');

        $deleteUserIds = User::query()
            ->whereNotIn('id', $keptUserIds)
            ->pluck('id');

        $tables = [
            'reports',
            'suggestion_comments',
            'suggestion_reports',
            'suggestion_user_upvotes',
            'suggestions',
            'appointment_requests',
            'messages',
            'password_reset_requests',
            'customer_satisfaction_surveys',
            'notifications',
            'personal_access_tokens',
            'sessions',
            'password_reset_tokens',
            'jobs',
            'failed_jobs',
            'job_batches',
        ];

        $counts = [];
        foreach ($tables as $table) {
            $counts[$table] = Schema::hasTable($table) ? (int) DB::table($table)->count() : 0;
        }
        $counts['users_to_delete'] = $deleteUserIds->count();
        $counts['users_to_keep'] = $keptUserIds->count();

        $this->newLine();
        $this->components->info('Defense cleanup preview');
        foreach ($counts as $table => $count) {
            $this->line(str_pad($table, 34, '.').$count);
        }

        if (! $this->option('force')) {
            $this->newLine();
            $this->warn('Dry run only. Run again with --force to execute.');
            return self::SUCCESS;
        }

        if (! $this->confirm('Delete the previewed test users and data?')) {
            $this->warn('Cleanup cancelled.');
            return self::SUCCESS;
        }

        DB::transaction(function () use ($deleteUserIds): void {
            foreach ([
                'suggestion_comments',
                'suggestion_reports',
                'suggestion_user_upvotes',
                'reports',
                'suggestions',
                'appointment_requests',
                'messages',
                'password_reset_requests',
                'customer_satisfaction_surveys',
            ] as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->delete();
                }
            }

            if (Schema::hasTable('notifications')) {
                DB::table('notifications')->delete();
            }
            if (Schema::hasTable('personal_access_tokens')) {
                DB::table('personal_access_tokens')->delete();
            }
            if (Schema::hasTable('sessions')) {
                DB::table('sessions')->delete();
            }
            if (Schema::hasTable('password_reset_tokens')) {
                DB::table('password_reset_tokens')->delete();
            }
            foreach (['jobs', 'failed_jobs', 'job_batches'] as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->delete();
                }
            }

            if ($deleteUserIds->isNotEmpty()) {
                User::query()->whereIn('id', $deleteUserIds)->delete();
            }
        });

        \Illuminate\Support\Facades\Cache::flush();
        $this->components->info('Defense cleanup completed.');
        $this->line('Kept official/admin/coordinator accounts and the two protected PSU accounts.');

        return self::SUCCESS;
    }
}
