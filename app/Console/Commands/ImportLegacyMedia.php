<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImportLegacyMedia extends Command
{
    protected $signature = 'media:import-legacy
        {--source= : Legacy Laravel project/storage root}
        {--dry-run : Validate files without copying them}';

    protected $description = 'Copy legacy uploaded media referenced by the TiDB/MySQL records';

    public function handle(): int
    {
        $sourceRoot = realpath((string) ($this->option('source') ?: env('LEGACY_STORAGE_PATH', '')));
        if ($sourceRoot === false || !is_dir($sourceRoot)) {
            $this->error('Set LEGACY_STORAGE_PATH or pass --source to the legacy storage root.');
            return self::FAILURE;
        }

        config(['database.connections.legacy' => [
            'driver' => 'mysql',
            'host' => env('LEGACY_DB_HOST'),
            'port' => env('LEGACY_DB_PORT', 4000),
            'database' => env('LEGACY_DB_DATABASE'),
            'username' => env('LEGACY_DB_USERNAME'),
            'password' => env('LEGACY_DB_PASSWORD'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
        ]]);

        $source = DB::connection('legacy');
        $copied = 0;
        $missing = 0;

        $userColumns = $source->getSchemaBuilder()->getColumnListing('users');
        $userFields = array_values(array_intersect([
            'profile_photo_path',
            'id_image_path',
            'selfie_image_path',
        ], $userColumns));
        foreach ($source->table('users')->select($userFields)->get() as $user) {
            foreach ($userFields as $field) {
                if (blank($user->{$field})) {
                    continue;
                }
                $disk = $field === 'profile_photo_path'
                    ? config('filesystems.user_public_disk')
                    : config('filesystems.user_private_disk');
                $this->copyMedia($sourceRoot, $user->{$field}, $disk, $copied, $missing);
            }
        }

        if ($source->getSchemaBuilder()->hasTable('reports')) {
            foreach ($source->table('reports')->pluck('evidence') as $evidence) {
                foreach ($this->decodePaths($evidence) as $path) {
                    $this->copyMedia($sourceRoot, $path, 'local', $copied, $missing);
                }
            }
        }

        if ($source->getSchemaBuilder()->hasTable('suggestions')
            && in_array('image_path', $source->getSchemaBuilder()->getColumnListing('suggestions'), true)) {
            foreach ($source->table('suggestions')->pluck('image_path') as $path) {
                if (filled($path)) {
                    $this->copyMedia($sourceRoot, $path, config('filesystems.user_public_disk'), $copied, $missing);
                }
            }
        }

        $action = $this->option('dry-run') ? 'Validated' : 'Copied';
        $this->info("{$action} {$copied} media file(s); {$missing} missing file(s).");
        return self::SUCCESS;
    }

    private function copyMedia(
        string $sourceRoot,
        string $path,
        string $disk,
        int &$copied,
        int &$missing
    ): void {
        $clean = ltrim(str_replace('\\', '/', trim($path)), '/');
        $clean = preg_replace('#^(storage/app/public/|storage/app/private/|storage/|public/)#', '', $clean);
        if ($clean === '' || str_contains($clean, '..')) {
            $missing++;
            return;
        }

        $candidates = [
            $sourceRoot . DIRECTORY_SEPARATOR . $clean,
            $sourceRoot . '/storage/app/public/' . $clean,
            $sourceRoot . '/storage/app/private/' . $clean,
            $sourceRoot . '/public/storage/' . $clean,
            $sourceRoot . '/public/' . $clean,
        ];
        $realPath = null;
        foreach ($candidates as $candidate) {
            $resolved = realpath($candidate);
            if ($resolved !== false && is_file($resolved)) {
                $realPath = $resolved;
                break;
            }
        }
        if ($realPath === null) {
            $this->warn("Missing media: {$clean}");
            $missing++;
            return;
        }

        $copied++;
        if (!$this->option('dry-run')) {
            Storage::disk($disk)->put($clean, fopen($realPath, 'rb'));
        }
    }

    private function decodePaths(mixed $value): array
    {
        if (is_array($value)) {
            return array_values(array_filter($value, 'is_string'));
        }
        if (!is_string($value) || trim($value) === '') {
            return [];
        }
        $decoded = json_decode($value, true);
        return is_array($decoded) ? array_values(array_filter($decoded, 'is_string')) : [$value];
    }
}
