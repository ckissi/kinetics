<?php

namespace Tests\Unit;

use App\Models\EffectPrompt;
use PHPUnit\Framework\TestCase;

class PromptTeaserTest extends TestCase
{
    protected function prompt(string $content): EffectPrompt
    {
        $prompt = new EffectPrompt();
        $prompt->content = $content;

        return $prompt;
    }

    public function test_it_shows_a_share_of_a_typical_prompt(): void
    {
        // 300 characters, close to the median prompt in the library.
        $content = str_repeat('word ', 60);

        $teaser = $this->prompt($content)->teaser();

        $this->assertLessThan(mb_strlen(trim($content)), mb_strlen($teaser));
        $this->assertLessThanOrEqual(161, mb_strlen($teaser));
        $this->assertStringEndsWith('…', $teaser);
    }

    public function test_it_never_gives_away_more_than_the_cap(): void
    {
        $content = str_repeat('word ', 200); // ~1000 characters

        $this->assertLessThanOrEqual(161, mb_strlen($this->prompt($content)->teaser()));
    }

    public function test_it_still_shows_something_useful_for_a_short_prompt(): void
    {
        $content = str_repeat('word ', 40); // ~200 characters

        $teaser = $this->prompt($content)->teaser();

        $this->assertGreaterThanOrEqual(70, mb_strlen($teaser));
    }

    public function test_it_cuts_on_a_word_boundary(): void
    {
        $content = 'Alpha bravo charlie delta echo foxtrot golf hotel india juliet kilo lima mike '
            . 'november oscar papa quebec romeo sierra tango uniform victor whiskey xray yankee zulu '
            . 'and then some more words to push this well past the cap so a cut is required.';

        $teaser = $this->prompt($content)->teaser();

        $this->assertStringEndsWith('…', $teaser);
        // No half-word before the ellipsis.
        $withoutEllipsis = rtrim($teaser, '…');
        $this->assertStringContainsString($withoutEllipsis, $content);
        $this->assertSame($withoutEllipsis, rtrim($withoutEllipsis));
    }

    public function test_a_prompt_shorter_than_the_minimum_is_returned_whole(): void
    {
        $content = 'Make it bounce.';

        $this->assertSame('Make it bounce.', $this->prompt($content)->teaser());
    }
}
