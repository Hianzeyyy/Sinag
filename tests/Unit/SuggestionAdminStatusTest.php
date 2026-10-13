<?php

namespace Tests\Unit;

use App\Models\Suggestion;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class SuggestionAdminStatusTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_new_status_is_hidden_only_after_one_day(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-15 12:00:00'));

        $recentSuggestion = (new Suggestion())->forceFill([
            'status' => 'New',
            'created_at' => now()->subHours(23),
        ]);
        $expiredSuggestion = (new Suggestion())->forceFill([
            'status' => 'New',
            'created_at' => now()->subDay()->subSecond(),
        ]);

        $this->assertSame('New', $recentSuggestion->admin_status_label);
        $this->assertSame('', $expiredSuggestion->admin_status_label);
    }

    public function test_other_statuses_remain_visible_after_one_day(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-01-15 12:00:00'));

        $suggestion = (new Suggestion())->forceFill([
            'status' => 'Pending',
            'created_at' => now()->subDays(10),
        ]);

        $this->assertSame('Pending', $suggestion->admin_status_label);
    }
}
