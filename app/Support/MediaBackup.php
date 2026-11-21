<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use ZipArchive;

class MediaBackup
{
    private const SOURCE_DISKS = ['public', 'public_s3', 'local', 'private_s3'];

    private const UPLOAD_DIRECTORIES = [
        'profile-photos',
        'verification',
        'password-reset-requests',
        'suggestions',
        'evidence',
    ];

    private const IMAGE_EXTENSIONS = [
        'avif', 'bmp', 'gif', 'heic', 'heif', 'jpeg', 'jpg',
        'png', 'svg', 'tif', 'tiff', 'webp',
    ];

    public function syncImagesToConfiguredStorage(): int
    {
        $publicTarget = config('filesystems.user_public_disk');
        $privateTarget = config('filesystems.user_private_disk');

        if ($publicTarget !== 'public_s3' || $privateTarget !== 'private_s3') {
            throw new MediaBackupException(
                'Set USER_PUBLIC_MEDIA_DISK=public_s3 and USER_PRIVATE_MEDIA_DISK=private_s3 before syncing existing uploads.'
            );
        }

        $copied = [];

        foreach ($this->sourceDisks() as $sourceName) {
            $source = Storage::disk($sourceName);
            foreach (self::UPLOAD_DIRECTORIES as $directory) {
                foreach ($source->allFiles($directory) as $path) {
                    $this->copyImageToTarget($sourceName, $path, $copied);
                }
            }
        }

        foreach ($this->referencedPaths() as $path) {
            foreach ($this->sourceDisks() as $sourceName) {
                $this->copyImageToTarget($sourceName, $path, $copied);
            }
        }

        return count($copied);
    }

    /**
     * Create an archive of uploaded images and store it on the backup disk.
     *
     * @return array{path: string, count: int}
     */
    public function createArchive(): array
    {
        if (!class_exists(ZipArchive::class)) {
            throw new MediaBackupException('The PHP ZIP extension is required to create media backups.');
        }

        $archivePath = tempnam(sys_get_temp_dir(), 'sinag-image-backup-');
        if ($archivePath === false) {
            throw new MediaBackupException('A temporary file for the image backup could not be created.');
        }

        $zip = new ZipArchive();
        $opened = $zip->open($archivePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($opened !== true) {
            @unlink($archivePath);
            throw new MediaBackupException('The image backup archive could not be opened.');
        }

        $temporaryFiles = [];
        $added = [];

        try {
            foreach ($this->sourceDisks() as $diskName) {
                $disk = Storage::disk($diskName);

                foreach (self::UPLOAD_DIRECTORIES as $directory) {
                    foreach ($disk->allFiles($directory) as $path) {
                        $this->addImage($zip, $diskName, $path, $temporaryFiles, $added);
                    }
                }
            }

            foreach ($this->referencedPaths() as $path) {
                foreach ($this->sourceDisks() as $diskName) {
                    $this->addImage($zip, $diskName, $path, $temporaryFiles, $added);
                }
            }

            if (!$zip->close()) {
                throw new MediaBackupException('The image backup archive could not be finalized.');
            }

            $destination = 'media-backups/user-images-' . now()->format('Ymd-His') . '.zip';
            $stream = fopen($archivePath, 'rb');
            if ($stream === false) {
                throw new MediaBackupException('The completed image backup could not be read.');
            }

            try {
                $stored = Storage::disk('media-backups')->put($destination, $stream);
            } finally {
                fclose($stream);
            }

            if (!$stored) {
                throw new MediaBackupException('The image backup could not be stored on the configured backup disk.');
            }

            return ['path' => $destination, 'count' => count($added)];
        } catch (\Throwable $exception) {
            $zip->close();
            throw $exception;
        } finally {
            foreach ($temporaryFiles as $temporaryFile) {
                @unlink($temporaryFile);
            }
            @unlink($archivePath);
        }
    }

    /**
     * @param array<int, string> $temporaryFiles
     * @param array<string, bool> $added
     */
    private function addImage(
        ZipArchive $zip,
        string $diskName,
        string $path,
        array &$temporaryFiles,
        array &$added
    ): void {
        if (CloudinaryMedia::hasReferenceScheme($path)) {
            $asset = CloudinaryMedia::parseReference($path);
            if ($asset === null || $asset['resource_type'] !== 'image') {
                return;
            }

            $archiveName = 'cloudinary/'.$asset['delivery_type'].'/'.$asset['public_id'];
            if (isset($added[$archiveName])) {
                return;
            }

            $contents = app(CloudinaryMedia::class)->downloadContents($path);
            if ($contents === null) {
                throw new MediaBackupException('Cloudinary image could not be downloaded for the media backup.');
            }

            $temporaryFile = tempnam(sys_get_temp_dir(), 'sinag-image-');
            if ($temporaryFile === false) {
                throw new MediaBackupException('A temporary image file for the backup could not be created.');
            }
            $temporaryFiles[] = $temporaryFile;

            if (file_put_contents($temporaryFile, $contents) !== strlen($contents)) {
                throw new MediaBackupException('Cloudinary image could not be written to the backup archive.');
            }

            if (!$zip->addFile($temporaryFile, $archiveName)) {
                throw new MediaBackupException("Cloudinary image could not be added to the archive: {$archiveName}");
            }

            $added[$archiveName] = true;

            return;
        }

        $path = $this->normalizePath($path);
        if ($path === '') {
            return;
        }

        $disk = Storage::disk($diskName);
        if (!$disk->exists($path)) {
            return;
        }

        $archiveName = $diskName . '/' . $path;
        if (isset($added[$archiveName]) || !$this->isImage($disk, $path)) {
            return;
        }

        $source = $disk->readStream($path);
        if (!is_resource($source)) {
            throw new MediaBackupException("Image file could not be read from {$diskName}: {$path}");
        }

        $temporaryFile = tempnam(sys_get_temp_dir(), 'sinag-image-');
        if ($temporaryFile === false) {
            fclose($source);
            throw new MediaBackupException('A temporary image file for the backup could not be created.');
        }
        $temporaryFiles[] = $temporaryFile;

        $destination = fopen($temporaryFile, 'wb');
        if ($destination === false) {
            fclose($source);
            throw new MediaBackupException('A temporary image file for the backup could not be opened.');
        }

        try {
            if (stream_copy_to_stream($source, $destination) === false) {
                throw new MediaBackupException("Image file could not be copied into the archive: {$path}");
            }
        } finally {
            fclose($source);
            fclose($destination);
        }

        if (!$zip->addFile($temporaryFile, $archiveName)) {
            throw new MediaBackupException("Image file could not be added to the archive: {$archiveName}");
        }

        $added[$archiveName] = true;
    }

    /**
     * @param array<string, bool> $copied
     */
    private function copyImageToTarget(string $sourceName, string $path, array &$copied): void
    {
        $path = $this->normalizePath($path);
        if ($path === '') {
            return;
        }

        $targetName = in_array(
            strtolower((string) strtok($path, '/')),
            ['profile-photos', 'suggestions'],
            true
        ) ? 'public_s3' : 'private_s3';
        $copyKey = $targetName . '/' . $path;

        if (isset($copied[$copyKey])) {
            return;
        }

        $source = Storage::disk($sourceName);
        if (!$source->exists($path) || !$this->isImage($source, $path)) {
            return;
        }

        $target = Storage::disk($targetName);
        if ($target->exists($path)) {
            $copied[$copyKey] = true;
            return;
        }

        $stream = $source->readStream($path);
        if (!is_resource($stream)) {
            throw new MediaBackupException("Image file could not be read from {$sourceName}: {$path}");
        }

        try {
            if (!$target->writeStream($path, $stream)) {
                throw new MediaBackupException("Image file could not be copied to {$targetName}: {$path}");
            }
        } finally {
            fclose($stream);
        }

        $copied[$copyKey] = true;
    }

    private function isImage(\Illuminate\Contracts\Filesystem\Filesystem $disk, string $path): bool
    {
        $mimeType = $disk->mimeType($path);

        return (is_string($mimeType) && str_starts_with($mimeType, 'image/'))
            || in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), self::IMAGE_EXTENSIONS, true);
    }

    /**
     * Include referenced files outside the standard upload folders (including legacy paths).
     *
     * @return array<int, string>
     */
    private function referencedPaths(): array
    {
        $paths = [];

        if (Schema::hasTable('users')) {
            $columns = array_values(array_filter(
                ['profile_photo_path', 'id_image_path', 'selfie_image_path'],
                fn (string $column) => Schema::hasColumn('users', $column)
            ));

            if ($columns !== []) {
                foreach (DB::table('users')->select($columns)->cursor() as $user) {
                    foreach ($columns as $column) {
                        if (filled($user->{$column})) {
                            $paths[] = $user->{$column};
                        }
                    }
                }
            }
        }

        if (Schema::hasTable('suggestions') && Schema::hasColumn('suggestions', 'image_path')) {
            foreach (DB::table('suggestions')->whereNotNull('image_path')->pluck('image_path') as $path) {
                $paths[] = $path;
            }
        }

        if (Schema::hasTable('reports') && Schema::hasColumn('reports', 'evidence')) {
            foreach (DB::table('reports')->whereNotNull('evidence')->pluck('evidence') as $evidence) {
                $decoded = is_string($evidence) ? json_decode($evidence, true) : null;
                foreach (is_array($decoded) ? $decoded : [$evidence] as $path) {
                    if (is_string($path) && filled($path)) {
                        $paths[] = $path;
                    }
                }
            }
        }

        if (Schema::hasTable('password_reset_requests') && Schema::hasColumn('password_reset_requests', 'id_picture_path')) {
            foreach (DB::table('password_reset_requests')->whereNotNull('id_picture_path')->pluck('id_picture_path') as $path) {
                $paths[] = $path;
            }
        }

        return array_values(array_unique($paths));
    }

    private function normalizePath(string $path): string
    {
        $clean = ltrim(str_replace('\\', '/', trim($path)), '/');
        if (preg_match(
            '#(?:^|/)(?:storage/app/(?:public|private)|app/(?:public|private)|public/storage)/#i',
            $clean,
            $match,
            PREG_OFFSET_CAPTURE
        )) {
            $clean = substr($clean, $match[0][1] + strlen($match[0][0]));
        }
        $segments = explode('/', $clean);

        return $clean === '' || in_array('', $segments, true) || in_array('.', $segments, true) || in_array('..', $segments, true)
            ? ''
            : $clean;
    }

    private function sourceDisks(): array
    {
        $configured = [
            config('filesystems.user_public_disk'),
            config('filesystems.user_private_disk'),
        ];

        return array_values(array_filter(
            self::SOURCE_DISKS,
            fn (string $diskName) => !str_ends_with($diskName, '_s3')
                || in_array($diskName, $configured, true)
        ));
    }
}