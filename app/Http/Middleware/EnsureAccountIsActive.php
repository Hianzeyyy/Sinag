<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $status = strtolower(trim((string) ($user?->account_status ?? 'active')));

        if (!$user || in_array($status, ['active', 'approved'], true)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            $token = $user->currentAccessToken();
            if ($token && method_exists($token, 'delete')) {
                $token->delete();
            }

            return response()->json(['message' => 'This account is no longer active.'], 401);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('error', 'This account is no longer active. Please contact the GAD office for assistance.');
    }
}
