<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DeleteConfiguredUserMigrationTest extends TestCase
{
    public function test_migration_deletes_only_the_configured_non_admin_user_and_cascades_records(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('email')->unique();
            $table->string('role');
            $table->string('profile_photo_path')->nullable();
            $table->string('id_image_path')->nullable();
            $table->string('selfie_image_path')->nullable();
        });

        Schema::create('reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('evidence')->nullable();
        });

        Schema::create('password_reset_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('id_picture_path');
        });

        Storage::fake('public');
        Storage::fake('local');
        config([
            'filesystems.user_public_disk' => 'public',
            'filesystems.user_private_disk' => 'local',
        ]);

        $targetId = DB::table('users')->insertGetId([
            'email' => 'target@example.test',
            'role' => 'student',
            'profile_photo_path' => 'profile-photos/target.png',
        ]);
        $unrelatedId = DB::table('users')->insertGetId([
            'email' => 'unrelated@example.test',
            'role' => 'student',
        ]);
        $adminId = DB::table('users')->insertGetId([
            'email' => 'admin@example.test',
            'role' => 'admin',
        ]);
        DB::table('reports')->insert([
            'user_id' => $targetId,
            'evidence' => '[]',
        ]);
        DB::table('password_reset_requests')->insert([
            'user_id' => $targetId,
            'id_picture_path' => 'password-reset-requests/target.png',
        ]);
        Storage::disk('public')->put('profile-photos/target.png', 'image');
        Storage::disk('local')->put('password-reset-requests/target.png', 'image');

        $previousEmail = getenv('USER_EMAIL_TO_DELETE');
        putenv('USER_EMAIL_TO_DELETE=target@example.test');
        try {
            $migration = require database_path('migrations/2026_11_20_000003_delete_configured_user.php');
            $migration->up();
        } finally {
            $previousEmail === false
                ? putenv('USER_EMAIL_TO_DELETE')
                : putenv('USER_EMAIL_TO_DELETE='.$previousEmail);
        }

        $this->assertDatabaseMissing('users', ['id' => $targetId]);
        $this->assertDatabaseHas('users', ['id' => $unrelatedId]);
        $this->assertDatabaseHas('users', ['id' => $adminId]);
        $this->assertDatabaseMissing('reports', ['user_id' => $targetId]);
        $this->assertDatabaseMissing('password_reset_requests', ['user_id' => $targetId]);
        Storage::disk('public')->assertMissing('profile-photos/target.png');
        Storage::disk('local')->assertMissing('password-reset-requests/target.png');

        putenv('USER_EMAIL_TO_DELETE=admin@example.test');
        try {
            $migration = require database_path('migrations/2026_11_20_000003_delete_configured_user.php');
            $migration->up();
            $this->fail('The migration should refuse to delete an administrator.');
        } catch (\RuntimeException $exception) {
            $this->assertSame(
                'The configured deletion target is an administrator; refusing to delete it.',
                $exception->getMessage()
            );
        } finally {
            $previousEmail === false
                ? putenv('USER_EMAIL_TO_DELETE')
                : putenv('USER_EMAIL_TO_DELETE='.$previousEmail);
        }

        $this->assertDatabaseHas('users', ['id' => $adminId]);
    }
}
