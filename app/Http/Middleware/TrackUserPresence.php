<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class TrackUserPresence
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->user() && Schema::hasColumn('users', 'last_seen_at')) {
            $request->user()->forceFill(['last_seen_at' => now()])->saveQuietly();
        }

        return $response;
    }
}