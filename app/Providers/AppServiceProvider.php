<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate; // Import Gate facade
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use App\Models\User; // Import User model
use App\Models\Report;
use App\Models\Suggestion;
use App\Models\AppointmentRequest;
use App\Models\Message;
use App\Models\PasswordResetRequest;
use App\Models\SuggestionReport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('password-reset-request', function ($request) {
            return Limit::perMinute(10)->by($request->ip() . '|' . strtolower((string) $request->input('email')));
        });

        RateLimiter::for('password-reset-status', function ($request) {
            return Limit::perMinute(30)->by($request->ip() . '|' . strtoupper((string) $request->input('reference_number')));
        });

        RateLimiter::for('password-reset-submit', function ($request) {
            return Limit::perMinute(20)->by($request->ip() . '|' . (string) $request->route('passwordResetRequest'));
        });

        view()->composer('layouts.app', function ($view): void {
            if (!auth()->check() || auth()->user()->role !== 'admin') {
                return;
            }

            $view->with('adminNotifications', [
                'users' => User::where('account_status', 'pending')->count(),
                'reports' => Report::whereNull('seen_at')->count(),
                'suggestions' => Suggestion::where('status', 'Pending')->count(),
                'appointments' => AppointmentRequest::where('status', 'pending')->count(),
                'messages' => Message::where('receiver_id', auth()->id())->whereNull('read_at')->count(),
                'password_resets' => PasswordResetRequest::where('status', 'pending')->count(),
                'suggestion_reports' => SuggestionReport::where('status', 'pending')->count(),
            ]);
        });
        // 1. Gate para sa GAD Administrator
        Gate::define('admin-access', function (User $user) {
            return strtolower(trim((string) $user->role)) === 'admin';
        });

        // 2. Gate para sa Verified Students
        Gate::define('student-access', function (User $user) {
            return $user->role === 'student' || $user->role === 'employee';
        });

        // 3. Gate para sa Campus Security (Optional but recommended base sa docs)
        Gate::define('security-access', function (User $user) {
            return $user->role === 'security';
        });
    }
}
