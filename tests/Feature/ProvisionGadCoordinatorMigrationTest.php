<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProvisionGadCoordinatorMigrationTest extends TestCase
{
    public function test_migration_provisions_coordinator_once_and_can_be_reversed(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->nullable();
            $table->string('account_type')->nullable();
            $table->string('account_status')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        $migration = require database_path('migrations/2026_11_12_000002_provision_gad_coordinator.php');

        $migration->up();
        $migration->up();

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'email' => 'gadcoordinator@psu.edu.ph',
            'role' => 'admin',
            'account_type' => 'coordinator',
            'account_status' => 'active',
        ]);

        $migration->down();

        $this->assertSame(0, DB::table('users')->count());
    }
}
