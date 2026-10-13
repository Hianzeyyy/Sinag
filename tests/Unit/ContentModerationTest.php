<?php

namespace Tests\Unit;

use App\Support\ContentModeration;
use PHPUnit\Framework\TestCase;

class ContentModerationTest extends TestCase
{
    public function test_a_harsh_word_inside_an_unrelated_word_is_not_flagged(): void
    {
        $this->assertFalse(ContentModeration::containsHarshLanguage(
            'Please include more student skills workshops in the annual program.'
        ));
    }

    public function test_harsh_words_are_still_flagged_when_spaced_out(): void
    {
        $this->assertTrue(ContentModeration::containsHarshLanguage('That is k i l l.'));
    }
}
