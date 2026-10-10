<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Session expired | {{ config('app.name') }}</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="bg-light">
    <main class="min-vh-100 d-flex align-items-center justify-content-center px-4">
        <section class="card border-0 shadow-sm rounded-4 text-center p-4 p-md-5" style="max-width: 520px;">
            <div class="display-6 fw-bold text-primary mb-3">419</div>
            <h1 class="h3 mb-3">Your session has expired</h1>
            <p class="text-secondary mb-4">
                The page was open for too long or your session was refreshed during an update.
                Reload the sign-in page and try again.
            </p>
            <a class="btn btn-primary rounded-pill px-4" href="{{ route('login') }}">Return to login</a>
        </section>
    </main>
</body>
</html>
