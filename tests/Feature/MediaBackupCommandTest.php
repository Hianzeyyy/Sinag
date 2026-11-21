<?php

namespace Tests\Feature;

use App\Support\MediaResolver;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use ZipArchive;

class MediaBackupCommandTest extends TestCase
{
    public function test_remote_public_images_are_resolvable_and_get_storage_urls(): void
    {
        Storage::fake('public');
        Storage::fake('public_s3');
        config([
            'filesystems.user_public_disk' => 'public_s3',
            'filesystems.disks.public_s3.bucket' => 'test-bucket',
            'filesystems.disks.public_s3.region' => 'us-east-1',
            'filesystems.disks.public_s3.key' => 'test-key',
            'filesystems.disks.public_s3.secret' => 'test-secret',
        ]);

        Storage::disk('public_s3')->put(
            'profile-photos/profile.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/l7sAAAAASUVORK5CYII=', true)
        );

        $this->assertTrue(MediaResolver::exists('profile-photos/profile.png', ['public', 'public_s3']));
        $this->assertNotNull(MediaResolver::fileResponse('profile-photos/profile.png', ['public', 'public_s3']));
        $this->assertNotNull(MediaResolver::url('profile-photos/profile.png', ['public', 'public_s3']));
    }

    public function test_unconfigured_s3_media_disks_are_skipped(): void
    {
        config([
            'filesystems.user_public_disk' => 'public_s3',
            'filesystems.disks.public_s3.bucket' => null,
            'filesystems.disks.public_s3.region' => null,
            'filesystems.disks.public_s3.key' => null,
            'filesystems.disks.public_s3.secret' => null,
        ]);

        $this->assertFalse(MediaResolver::exists('profile-photos/missing.png', ['public_s3']));
        $this->assertNull(MediaResolver::fileResponse('profile-photos/missing.png', ['public_s3']));
        $this->assertNull(MediaResolver::url('profile-photos/missing.png', ['public_s3']));
    }

    public function test_sync_command_moves_existing_images_to_the_configured_s3_disks(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        Storage::fake('public_s3');
        Storage::fake('private_s3');
        config([
            'filesystems.user_public_disk' => 'public_s3',
            'filesystems.user_private_disk' => 'private_s3',
        ]);

        $image = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/l7sAAAAASUVORK5CYII=', true);
        Storage::disk('public')->put('profile-photos/profile.png', $image);
        Storage::disk('local')->put('verification/id.png', $image);
        Storage::disk('local')->put('evidence/report.pdf', 'not an image');

        $this->artisan('media:sync-images-to-storage')
            ->expectsOutput('Copied 2 image(s) to configured S3-compatible storage.')
            ->assertSuccessful();

        Storage::disk('public_s3')->assertExists('profile-photos/profile.png');
        Storage::disk('private_s3')->assertExists('verification/id.png');
        Storage::disk('private_s3')->assertMissing('evidence/report.pdf');
    }

    public function test_manual_backup_archives_uploaded_images_from_configured_storage_and_excludes_documents(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('profile_photo_path')->nullable();
            $table->string('id_image_path')->nullable();
            $table->string('selfie_image_path')->nullable();
        });

        Storage::fake('public');
        Storage::fake('local');
        Storage::fake('media-backups');
        config([
            'filesystems.user_public_disk' => 'public',
            'filesystems.user_private_disk' => 'local',
        ]);

        $image = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/l7sAAAAASUVORK5CYII=', true);
        Storage::disk('public')->put('profile-photos/profile.png', $image);
        Storage::disk('local')->put('legacy-media/verification.jpg', $image);
        Storage::disk('local')->put('evidence/report.pdf', 'not an image');

        DB::table('users')->insert([
            'profile_photo_path' => null,
            'id_image_path' => 'legacy-media/verification.jpg',
            'selfie_image_path' => null,
        ]);

        $this->artisan('media:backup-images')->assertSuccessful();

        $archives = Storage::disk('media-backups')->allFiles('media-backups');
        $this->assertCount(1, $archives);

        $archive = new ZipArchive();
        $this->assertTrue($archive->open(Storage::disk('media-backups')->path($archives[0])));
        try {
            $entries = [];
            for ($index = 0; $index < $archive->numFiles; $index++) {
                $entries[] = $archive->getNameIndex($index);
            }
        } finally {
            $archive->close();
        }

        $this->assertContains('public/profile-photos/profile.png', $entries);
        $this->assertContains('local/legacy-media/verification.jpg', $entries);
        $this->assertNotContains('local/evidence/report.pdf', $entries);
    }
}
