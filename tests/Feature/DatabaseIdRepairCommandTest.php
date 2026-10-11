<?php

namespace Tests\Feature;

use Tests\TestCase;

class DatabaseIdRepairCommandTest extends TestCase
{
    public function test_auto_increment_repair_command_is_safe_for_sqlite(): void
    {
        $this->artisan('database:repair-auto-increment-ids')
            ->expectsOutput('Database driver does not require MySQL ID repair.')
            ->assertExitCode(0);
    }
}
