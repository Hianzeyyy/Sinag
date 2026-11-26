<?php

namespace App\Support;

use Cloudinary\Asset\BaseAsset;
use Cloudinary\Cloudinary;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class CloudinaryMedia
{
    private const REFERENCE_PATTERN = '#^cloudinary://(upload|authenticated)/(image|video|raw)/([A-Za-z0-9_./-]+)$#D';

    public function __construct(private Cloudinary $cloudinary) {}

    public static function isConfigured(): bool
    {
        $url = config('filesystems.disks.cloudinary.url');
        if (! is_string($url) || blank($url)) {
            return false;
        }

        $parts = parse_url($url);

        return is_array($parts)
            && ($parts['scheme'] ?? null) === 'cloudinary'
            && filled($parts['host'] ?? null)
            && filled($parts['user'] ?? null)
            && filled($parts['pass'] ?? null);
    }

    public static function hasReferenceScheme(?string $reference): bool
    {
        return is_string($reference) && str_starts_with($reference, 'cloudinary://');
    }

    /**
     * @return array{delivery_type: string, resource_type: string, public_id: string}|null
     */
    public static function parseReference(?string $reference): ?array
    {
        if (! is_string($reference)
            || ! preg_match(self::REFERENCE_PATTERN, $reference, $matches)) {
            return null;
        }

        $segments = explode('/', $matches[3]);
        if (in_array('', $segments, true) || in_array('.', $segments, true) || in_array('..', $segments, true)) {
            return null;
        }

        return [
            'delivery_type' => $matches[1],
            'resource_type' => $matches[2],
            'public_id' => $matches[3],
        ];
    }

    public function storeUploadedFile(UploadedFile $file, string $folder, bool $private): string
    {
        $path = $file->getRealPath();
        if (! is_string($path) || ! is_file($path)) {
            throw new RuntimeException('The uploaded media file is not available.');
        }

        $mimeType = $file->getMimeType();
        if (! is_string($mimeType) || $mimeType === '') {
            throw new RuntimeException('The uploaded media MIME type could not be determined.');
        }

        return $this->storeSource(
            $path,
            $folder,
            $private,
            $mimeType,
            $file->getClientOriginalExtension()
        );
    }

    public function storeContents(
        string $contents,
        string $folder,
        bool $private,
        string $mimeType,
        string $extension
    ): string {
        if ($contents === '') {
            throw new RuntimeException('The media file is empty.');
        }

        $source = 'data:'.$mimeType.';base64,'.base64_encode($contents);

        return $this->storeSource($source, $folder, $private, $mimeType, $extension);
    }

    private function storeSource(
        string $source,
        string $folder,
        bool $private,
        string $mimeType,
        string $extension
    ): string {
        $folder = trim(str_replace('\\', '/', $folder), '/');
        if ($folder === '' || str_contains($folder, '..') || preg_match('#[^A-Za-z0-9/_-]#', $folder)) {
            throw new RuntimeException('The media folder is invalid.');
        }

        $normalizedMimeType = strtolower($mimeType);
        $resourceType = str_starts_with($normalizedMimeType, 'image/')
            ? 'image'
            : ($normalizedMimeType === 'video/mp4' ? 'video' : 'raw');
        $publicId = 'sinag/'.$folder.'/'.Str::uuid();
        if ($resourceType === 'raw') {
            $extension = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $extension) ?? '');
            if ($extension === '') {
                throw new RuntimeException('The uploaded file extension could not be determined.');
            }
            $publicId .= '.'.$extension;
        }

        $deliveryType = $private ? 'authenticated' : 'upload';
        $result = $this->cloudinary->uploadApi()->upload($source, [
            'public_id' => $publicId,
            'resource_type' => $resourceType,
            'type' => $deliveryType,
            'overwrite' => false,
        ]);
        $storedPublicId = $result->offsetGet('public_id');
        if (! is_string($storedPublicId) || blank($storedPublicId)) {
            throw new RuntimeException('Cloudinary did not return a public ID for the uploaded media.');
        }

        return self::reference($deliveryType, $resourceType, $storedPublicId);
    }

    public function publicUrl(string $reference): ?string
    {
        $asset = self::parseReference($reference);
        if ($asset === null || $asset['delivery_type'] !== 'upload' || ! self::isConfigured()) {
            return null;
        }

        return (string) $this->asset($asset)->deliveryType('upload')->secure()->toUrl();
    }

    public function fileResponse(string $reference, array $headers = []): ?SymfonyResponse
    {
        $asset = self::parseReference($reference);
        if ($asset === null || ! self::isConfigured()) {
            return null;
        }

        $url = $this->deliveryUrl($asset);
        if ($asset['delivery_type'] === 'upload') {
            return redirect()->away($url, 302, $headers);
        }

        $headers = array_merge(['Cache-Control' => 'private, no-store'], $headers);

        try {
            $request = Http::timeout(30)->withOptions(['stream' => true]);
            $rangeHeaders = array_filter([
                'Range' => request()->header('Range'),
                'If-Range' => request()->header('If-Range'),
            ], static fn (?string $value): bool => is_string($value) && $value !== '');
            if ($rangeHeaders !== []) {
                $request = $request->withHeaders($rangeHeaders);
            }

            $remoteResponse = $request->get($url);
        } catch (ConnectionException $exception) {
            Log::error('Cloudinary private media delivery failed.', [
                'resource_type' => $asset['resource_type'],
                'exception' => $exception::class,
            ]);

            return response('Media storage is temporarily unavailable.', 503, $headers);
        }

        if (! $remoteResponse->successful()) {
            if ($remoteResponse->status() === 404) {
                Log::warning('Cloudinary private media was not found.', [
                    'resource_type' => $asset['resource_type'],
                    'status' => $remoteResponse->status(),
                ]);

                return null;
            }

            Log::error('Cloudinary private media delivery returned an error.', [
                'resource_type' => $asset['resource_type'],
                'status' => $remoteResponse->status(),
            ]);

            return response('Media storage is temporarily unavailable.', 502, $headers);
        }

        foreach (['Content-Type', 'Content-Length', 'Content-Range', 'Accept-Ranges', 'ETag', 'Last-Modified'] as $header) {
            $value = $remoteResponse->header($header);
            if (is_string($value) && $value !== '') {
                $headers[$header] = $value;
            }
        }
        $headers['X-Content-Type-Options'] = 'nosniff';
        $body = $remoteResponse->toPsrResponse()->getBody();

        return response()->stream(function () use ($body): void {
            try {
                while (! $body->eof()) {
                    echo $body->read(65536);
                    flush();
                }
            } finally {
                $body->close();
            }
        }, $remoteResponse->status(), $headers);
    }

    public function delete(string $reference): void
    {
        $asset = self::parseReference($reference);
        if ($asset === null) {
            throw new RuntimeException('The Cloudinary media reference is invalid.');
        }

        $result = $this->cloudinary->uploadApi()->destroy($asset['public_id'], [
            'resource_type' => $asset['resource_type'],
            'type' => $asset['delivery_type'],
            'invalidate' => true,
        ]);

        if (! in_array($result->offsetGet('result'), ['ok', 'not found'], true)) {
            throw new RuntimeException('Cloudinary did not confirm the media deletion.');
        }
    }

    public function downloadContents(string $reference): ?string
    {
        $asset = self::parseReference($reference);
        if ($asset === null || ! self::isConfigured()) {
            return null;
        }

        try {
            $response = Http::timeout(30)->get($this->deliveryUrl($asset));
        } catch (ConnectionException $exception) {
            Log::error('Cloudinary media download failed.', [
                'resource_type' => $asset['resource_type'],
                'exception' => $exception::class,
            ]);

            return null;
        }

        return $response->successful() ? $response->body() : null;
    }

    private function deliveryUrl(array $asset): string
    {
        $deliveryType = $asset['delivery_type'];
        $cloudinaryAsset = $this->asset($asset)
            ->deliveryType($deliveryType)
            ->secure();

        if ($deliveryType === 'authenticated') {
            $cloudinaryAsset->signUrl();
        }

        return (string) $cloudinaryAsset->toUrl();
    }

    private function asset(array $asset): BaseAsset
    {
        return match ($asset['resource_type']) {
            'video' => $this->cloudinary->video($asset['public_id']),
            'raw' => $this->cloudinary->raw($asset['public_id']),
            default => $this->cloudinary->image($asset['public_id']),
        };
    }

    private static function reference(string $deliveryType, string $resourceType, string $publicId): string
    {
        if (! in_array($deliveryType, ['upload', 'authenticated'], true)
            || ! in_array($resourceType, ['image', 'video', 'raw'], true)
            || self::parseReference("cloudinary://{$deliveryType}/{$resourceType}/{$publicId}") === null) {
            throw new RuntimeException('Cloudinary returned an invalid media reference.');
        }

        return "cloudinary://{$deliveryType}/{$resourceType}/{$publicId}";
    }
}
