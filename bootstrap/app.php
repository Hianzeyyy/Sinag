<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'profile.complete' => \App\Http\Middleware\EnsurePersonalDataComplete::class,
            'admin' => \Illuminate\Auth\Middleware\Authorize::class,
        ]);
        $middleware->append(\App\Http\Middleware\TrackUserPresence::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $exception, Request $request) {
            if ($request->isMethod('POST') && $request->is('submit-report')) {
                return redirect()
                    ->route('student.report')
                    ->withInput()
                    ->withErrors([
                        'report' => 'The report could not be submitted right now. Please try again or contact the GAD office if the problem continues.',
                    ]);
            }
        });
    })->create();

/*
|--------------------------------------------------------------------------
| Vercel Serverless Storage Path
|--------------------------------------------------------------------------
|
| When running on Vercel's read-only filesystem, redirect storage to /tmp
| and ensure essential runtime cache/view directories exist.
|
*/
if (isset($_ENV['VERCEL_REGION']) || isset($_SERVER['VERCEL_REGION']) || env('VERCEL_REGION')) {
    $storagePath = '/tmp/storage';
    $app->useStoragePath($storagePath);

    $directories = [
        $storagePath . '/app',
        $storagePath . '/app/public',
        $storagePath . '/framework',
        $storagePath . '/framework/cache',
        $storagePath . '/framework/cache/data',
        $storagePath . '/framework/sessions',
        $storagePath . '/framework/views',
        $storagePath . '/logs',
    ];

    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
    }
}

return $app;
