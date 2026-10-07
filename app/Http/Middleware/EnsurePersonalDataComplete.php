<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePersonalDataComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && !$user->hasCompletedPersonalData()) {
            return redirect()
                ->route('personal-data.edit')
                ->with('profile_prompt', 'Please complete your required personal information before using this feature.');
        }

        return $next($request);
    }
}