<?php

namespace App\Console\Commands;

use App\Support\MediaBackup;
use App\Support\MediaBackupException;
use Illuminate\Console\Command;

class SyncUploadedImagesToStorage extends Command
{
    protected $signature = 'media:sync-images-to-storage';

    protected $description = 'Copy existing uploaded images to the configured S3-compatible storage';

    public function handle(MediaBackup $mediaBackup): int
    {
        try {
            $copied = $mediaBackup->syncImagesToConfiguredStorage();
        } catch (MediaBackupException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info("Copied {$copied} image(s) to configured S3-compatible storage.");

        return self::SUCCESS;
    }
}
