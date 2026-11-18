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
            $redirectTo = $request->routeIs('student.boses', 'suggestions.store', 'suggestions.update', 'suggestions.comment', 'suggestions.report')
                ? route('personal-data.edit', ['return_to_suggestions' => 1])
                : route('personal-data.edit');

            return redirect()
                ->to($redirectTo)
                ->with('personal_data_required', 'Complete your personal information before using this feature.')
                ->with('profile_prompt', 'Please complete your required personal information before using this feature.');
        }

        return $next($request);
    }
}