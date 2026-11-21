<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\FakesCloudinaryMedia;
use Tests\TestCase;

class MigrateMediaToCloudinaryTest extends TestCase
{
    use FakesCloudinaryMedia;

    public function test_media_migration_dry_run_preserves_sources_and_real_run_updates_references(): void
    {
        $this->fakeCloudinaryMedia();
        Storage::fake('public');
        Storage::fake('local');
        config([
            'filesystems.user_public_disk' => 'public',
            'filesystems.user_private_disk' => 'local',
        ]);

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('profile_photo_path')->nullable();
            $table->string('id_image_path')->nullable();
            $table->string('selfie_image_path')->nullable();
        });
        Schema::create('password_reset_requests', function (Blueprint $table): void {
            $table->id();
            $table->string('id_picture_path')->nullable();
        });
        Schema::create('suggestions', function (Blueprint $table): void {
            $table->id();
            $table->string('image_path')->nullable();
        });
        Schema::create('reports', function (Blueprint $table): void {
            $table->id();
            $table->json('evidence')->nullable();
        });

        $image = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/l7sAAAAASUVORK5CYII=', true);
        Storage::disk('public')->put('profile-photos/profile.png', $image);
        Storage::disk('public')->put('suggestions/suggestion.png', $image);
        Storage::disk('local')->put('verification/id/student.png', $image);
        Storage::disk('local')->put('password-reset-requests/student.png', $image);
        Storage::disk('local')->put('evidence/report.png', $image);
        Storage::disk('local')->put('evidence/report.pdf', "%PDF-1.4\nSINAG evidence\n");
        Storage::disk('local')->put('evidence/legacy.png', $image);
        Storage::disk('local')->put('evidence/legacy-scalar.png', $image);

        DB::table('users')->insert([
            'profile_photo_path' => 'profile-photos/profile.png',
            'id_image_path' => 'verification/id/student.png',
            'selfie_image_path' => 'verification/id/student.png',
        ]);
        DB::table('password_reset_requests')->insert([
            'id_picture_path' => 'password-reset-requests/student.png',
        ]);
        DB::table('suggestions')->insert([
            'image_path' => 'suggestions/suggestion.png',
        ]);
        DB::table('reports')->insert([
            'evidence' => json_encode(['evidence/report.png', 'evidence/report.pdf']),
        ]);
        DB::table('reports')->insert([
            'evidence' => 'evidence/legacy.png',
        ]);
        DB::table('reports')->insert([
            'evidence' => json_encode('evidence/legacy-scalar.png'),
        ]);

        $this->artisan('media:migrate-to-cloudinary', ['--dry-run' => true])->assertSuccessful();
        $this->assertSame(
            'profile-photos/profile.png',
            DB::table('users')->value('profile_photo_path')
        );

        $this->artisan('media:migrate-to-cloudinary')->assertSuccessful();

        $user = DB::table('users')->first();
        $this->assertStringStartsWith('cloudinary://upload/image/sinag/profile-photos/', $user->profile_photo_path);
        $this->assertStringStartsWith('cloudinary://authenticated/image/sinag/verification/id/', $user->id_image_path);
        $this->assertSame($user->id_image_path, $user->selfie_image_path);
        $this->assertStringStartsWith(
            'cloudinary://authenticated/image/sinag/password-reset-requests/',
            DB::table('password_reset_requests')->value('id_picture_path')
        );
        $this->assertStringStartsWith('cloudinary://upload/image/sinag/suggestions/', DB::table('suggestions')->value('image_path'));
        $evidence = json_decode(DB::table('reports')->value('evidence'), true);
        $this->assertStringStartsWith('cloudinary://authenticated/image/sinag/reports/evidence/', $evidence[0]);
        $this->assertStringStartsWith('cloudinary://authenticated/raw/sinag/reports/evidence/', $evidence[1]);
        $this->assertStringEndsWith('.pdf', $evidence[1]);
        $legacyEvidence = json_decode(DB::table('reports')->where('id', 2)->value('evidence'), true);
        $this->assertStringStartsWith('cloudinary://authenticated/image/sinag/reports/evidence/', $legacyEvidence[0]);
        $legacyScalarEvidence = json_decode(DB::table('reports')->where('id', 3)->value('evidence'), true);
        $this->assertStringStartsWith('cloudinary://authenticated/image/sinag/reports/evidence/', $legacyScalarEvidence[0]);

        Storage::disk('public')->assertExists('profile-photos/profile.png');
        Storage::disk('public')->assertExists('suggestions/suggestion.png');
        Storage::disk('local')->assertExists('verification/id/student.png');
        Storage::disk('local')->assertExists('password-reset-requests/student.png');
        Storage::disk('local')->assertExists('evidence/report.png');
        Storage::disk('local')->assertExists('evidence/report.pdf');
        Storage::disk('local')->assertExists('evidence/legacy.png');
        Storage::disk('local')->assertExists('evidence/legacy-scalar.png');
    }
}
