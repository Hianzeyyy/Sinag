<?php

namespace App\Support;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MediaResolver
{
    /**
     * Resolve a stored relative path across public and private disks.
     */
    public static function resolvePath(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $clean = ltrim(str_replace('\\', '/', $path), '/');

        $candidates = [
            Storage::disk('public')->path($clean),
            Storage::disk('local')->path($clean),
            storage_path('app/public/' . $clean),
            storage_path('app/private/' . $clean),
            storage_path('app/' . $clean),
            public_path('storage/' . $clean),
            public_path($clean),
        ];

        if (str_starts_with($clean, 'public/')) {
            $stripped = substr($clean, 7);
            $candidates[] = storage_path('app/public/' . $stripped);
            $candidates[] = Storage::disk('public')->path($stripped);
            $candidates[] = storage_path('app/' . $clean);
        }

        if (str_starts_with($clean, 'private/')) {
            $stripped = substr($clean, 8);
            $candidates[] = storage_path('app/private/' . $stripped);
            $candidates[] = Storage::disk('local')->path($stripped);
            $candidates[] = storage_path('app/' . $clean);
        }

        if (str_starts_with($clean, 'storage/')) {
            $stripped = substr($clean, 8);
            $candidates[] = storage_path('app/public/' . $stripped);
            $candidates[] = public_path($clean);
        }

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Generate an SVG avatar for a user based on their name and role.
     */
    public static function defaultAvatarSvg(string $name, string $role = 'student'): string
    {
        $name = trim($name);
        $initial = mb_strtoupper(mb_substr($name !== '' ? $name : 'User', 0, 1, 'UTF-8'), 'UTF-8');
        $isAdmin = strtolower(trim($role)) === 'admin';

        $bg = $isAdmin ? '#7c3aed' : '#8f72f5';
        $accent = $isAdmin ? '#facc15' : '#ffffff';

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 128" width="128" height="128">
    <defs>
        <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="{$bg}" />
            <stop offset="100%" stop-color="#4c1d95" />
        </linearGradient>
    </defs>
    <rect width="128" height="128" rx="64" fill="url(#grad)" />
    <text x="50%" y="54%" text-anchor="middle" dominant-baseline="middle"
          fill="{$accent}" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif"
          font-size="58" font-weight="700">{$initial}</text>
</svg>
SVG;
    }

    /**
     * Generate a placeholder SVG when an ID or verification image is missing.
     */
    public static function placeholderIdSvg(string $title = 'ID Image Unavailable'): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 260" width="400" height="260">
    <rect width="400" height="260" rx="16" fill="#f8fafc" stroke="#e2e8f0" stroke-width="2"/>
    <g fill="#94a3b8" transform="translate(176, 70)">
        <path d="M4 4a2 2 0 0 1 2-2h36a2 2 0 0 1 2 2v28a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4zm6 6a4 4 0 1 0 8 0 4 4 0 0 0-8 0zm-2 16c0-2.67 5.33-4 8-4s8 1.33 8 4v2H8v-2zm16-16h16v2H24v-2zm0 6h16v2H24v-2zm0 6h10v2H24v-2z" transform="scale(2)"/>
    </g>
    <text x="200" y="180" text-anchor="middle" fill="#64748b" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="14" font-weight="600">{$title}</text>
    <text x="200" y="204" text-anchor="middle" fill="#94a3b8" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif" font-size="12">File was not found in storage</text>
</svg>
SVG;
    }

    /**
     * Return a file response if the path exists, or an SVG response fallback.
     */
    public static function fileOrFallback(?string $path, string $fallbackSvg, array $headers = []): Response|BinaryFileResponse
    {
        $realPath = self::resolvePath($path);

        if ($realPath !== null) {
            $mime = mime_content_type($realPath) ?: 'image/jpeg';
            $defaultHeaders = [
                'Content-Type' => $mime,
                'Cache-Control' => 'private, max-age=3600',
            ];

            return response()->file($realPath, array_merge($defaultHeaders, $headers));
        }

        return response($fallbackSvg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
