<?php

namespace App\Console\Commands;

use App\Support\MediaBackup;
use App\Support\MediaBackupException;
use Illuminate\Console\Command;

class BackupUploadedImages extends Command
{
    protected $signature = 'media:backup-images';

    protected $description = 'Create a ZIP backup of all uploaded user images';

    public function handle(MediaBackup $mediaBackup): int
    {
        try {
            $backup = $mediaBackup->createArchive();
        } catch (MediaBackupException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info("Backed up {$backup['count']} image(s) to {$backup['path']}.");

        return self::SUCCESS;
    }
}
