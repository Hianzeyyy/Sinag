<?php

namespace App\Console\Commands;

use App\Support\CloudinaryMedia;
use App\Support\MediaResolver;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class MigrateMediaToCloudinary extends Command
{
    protected $signature = 'media:migrate-to-cloudinary {--dry-run : Inspect readable legacy media without uploading or updating records}';

    protected $description = 'Move existing user-uploaded media references to Cloudinary without deleting source files';

    private array $referenceCache = [];

    private int $checked = 0;

    private int $failed = 0;

    public function handle(): int
    {
        if (! CloudinaryMedia::isConfigured()) {
            $this->error('Cloudinary is not configured. Set CLOUDINARY_URL in the environment and try again.');

            return self::FAILURE;
        }

        $this->migrateColumn('users', 'profile_photo_path', 'profile-photos', false);
        $this->migrateColumn('users', 'id_image_path', 'verification/id', true);
        $this->migrateColumn('users', 'selfie_image_path', 'verification/selfie', true);
        $this->migrateColumn('password_reset_requests', 'id_picture_path', 'password-reset-requests', true);
        $this->migrateColumn('suggestions', 'image_path', 'suggestions', false);
        $this->migrateReportEvidence();

        $this->info("Legacy media references examined: {$this->checked}. Unreadable or invalid references: {$this->failed}.");

        return $this->failed === 0 ? self::SUCCESS : self::FAILURE;
    }

    private function migrateColumn(string $table, string $column, string $folder, bool $private): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return;
        }

        DB::table($table)
            ->select(['id', $column])
            ->whereNotNull($column)
            ->orderBy('id')
            ->chunkById(100, function ($records) use ($table, $column, $folder, $private): void {
                foreach ($records as $record) {
                    $current = $record->{$column};
                    if (! is_string($current) || blank($current)) {
                        continue;
                    }

                    $next = $this->migrateReference($current, $folder, $private);
                    if (! $this->option('dry-run') && $next !== $current) {
                        DB::table($table)->where('id', $record->id)->update([$column => $next]);
                    }
                }
            });
    }

    private function migrateReportEvidence(): void
    {
        if (! Schema::hasTable('reports') || ! Schema::hasColumn('reports', 'evidence')) {
            return;
        }

        DB::table('reports')
            ->select(['id', 'evidence'])
            ->whereNotNull('evidence')
            ->orderBy('id')
            ->chunkById(100, function ($records): void {
                foreach ($records as $record) {
                    $rawEvidence = is_string($record->evidence) ? $record->evidence : null;
                    $evidence = is_array($record->evidence) ? $record->evidence : null;
                    if ($evidence === null && $rawEvidence !== null) {
                        $decodedEvidence = json_decode($rawEvidence, true);
                        if (is_array($decodedEvidence)) {
                            $evidence = $decodedEvidence;
                        } elseif (is_string($decodedEvidence) && filled($decodedEvidence)) {
                            $evidence = [$decodedEvidence];
                        } elseif (json_last_error() !== JSON_ERROR_NONE && filled($rawEvidence)) {
                            $evidence = [$rawEvidence];
                        }
                    }

                    if (! is_array($evidence)) {
                        $this->failed++;
                        $this->warn("Report {$record->id} has unreadable evidence metadata; it was left unchanged.");

                        continue;
                    }

                    $updated = false;
                    foreach ($evidence as $index => $path) {
                        if (! is_string($path) || blank($path)) {
                            continue;
                        }

                        $next = $this->migrateReference($path, 'reports/evidence', true);
                        if ($next !== $path) {
                            $evidence[$index] = $next;
                            $updated = true;
                        }
                    }

                    if ($updated && ! $this->option('dry-run')) {
                        DB::table('reports')->where('id', $record->id)->update([
                            'evidence' => json_encode($evidence, JSON_THROW_ON_ERROR),
                        ]);
                    }
                }
            });
    }

    private function migrateReference(string $path, string $folder, bool $private): string
    {
        if (CloudinaryMedia::hasReferenceScheme($path)) {
            if (CloudinaryMedia::parseReference($path) === null) {
                $this->failed++;
                $this->warn('An invalid Cloudinary media reference was found and left unchanged.');
            }

            return $path;
        }

        $this->checked++;
        $cacheKey = ($private ? 'private:' : 'public:').$path;
        if (isset($this->referenceCache[$cacheKey])) {
            return $this->referenceCache[$cacheKey];
        }

        $sourceDisks = $private
            ? ['local', config('filesystems.user_private_disk'), 'private_s3', 'public', 'public_s3']
            : ['public', config('filesystems.user_public_disk'), 'public_s3', 'local'];
        $sourceDisks = array_values(array_unique(array_filter($sourceDisks, 'is_string')));
        $contents = MediaResolver::contents($path, $sourceDisks);
        if ($contents === null) {
            $this->failed++;
            $this->warn('A legacy media file could not be read and its database reference was left unchanged.');

            return $path;
        }

        if ($this->option('dry-run')) {
            return $path;
        }

        $mimeType = (new \finfo(FILEINFO_MIME_TYPE))->buffer($contents);
        if (! is_string($mimeType) || $mimeType === '') {
            throw new RuntimeException('Unable to determine the MIME type of a legacy media file.');
        }

        $extension = pathinfo(str_replace('\\', '/', $path), PATHINFO_EXTENSION);
        $reference = app(CloudinaryMedia::class)->storeContents(
            $contents,
            $folder,
            $private,
            $mimeType,
            $extension
        );
        $this->referenceCache[$cacheKey] = $reference;

        return $reference;
    }
}
