<?php

namespace Tests\Feature;

use App\Models\Effect;
use Database\Seeders\EffectLibrarySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EffectLibraryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(EffectLibrarySeeder::class);
    }

    public function test_the_seeder_loads_the_whole_library(): void
    {
        $this->assertSame(3, \App\Models\Category::count());
        $this->assertSame(153, Effect::count());

        // Every effect ships CSS; React is optional.
        $this->assertSame(153, \App\Models\EffectSnippet::where('type', 'css')->count());
        $this->assertSame(99, \App\Models\EffectSnippet::where('type', 'react')->count());

        // Prompts are Pro content and arrive only via kinetics:sync-pro.
        $this->assertSame(0, \App\Models\EffectPrompt::count());

        $this->assertSame(0, Effect::doesntHave('keywords')->count());
    }

    public function test_the_home_page_renders_every_card(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Motion that', false);

        $html = $response->getContent();

        $this->assertSame(153, substr_count($html, '<div class="card">'));
        $this->assertSame(153, substr_count($html, '<div class="name">'));
        $this->assertSame(3, substr_count($html, 'class="gallery"'));
    }

    public function test_effects_without_react_do_not_render_a_react_tab(): void
    {
        $cssOnly = Effect::where('slug', 'orbital-action-menu')->firstOrFail();

        $this->assertNull($cssOnly->snippets->firstWhere('type', 'react'));

        $html = $this->get('/')->getContent();

        // 99 effects have React, each rendering one tab and one pane.
        $this->assertSame(99, substr_count($html, '<pre data-lang="react"'));
        $this->assertSame(99, substr_count($html, '<button class="code-tab" data-lang="react"'));
    }

    public function test_prompt_text_never_reaches_a_visitor_without_pro(): void
    {
        $secret = 'PROMPT-BODY-THAT-MUST-NOT-LEAK';

        Effect::where('slug', 'magnetic-button')->firstOrFail()->prompts()->create([
            'type' => 'general',
            'content' => $secret,
            'access_level' => \App\Enums\AccessLevel::Pro,
        ]);

        $html = $this->get('/')->getContent();

        $this->assertStringNotContainsString($secret, $html);

        // The tab still advertises that a prompt exists.
        $this->assertStringContainsString('data-lang="prompt"', $html);
        $this->assertStringContainsString('pro-locked', $html);
    }

    public function test_the_duplicated_pulse_badge_keeps_both_cards(): void
    {
        $badges = Effect::where('name', 'Pulse Badge')->orderBy('id')->get();

        $this->assertCount(2, $badges);
        $this->assertSame(['pulse-badge', 'pulse-badge-2'], $badges->pluck('slug')->all());
        $this->assertNotSame(
            $badges[0]->snippets->firstWhere('type', 'css')->content,
            $badges[1]->snippets->firstWhere('type', 'css')->content,
        );
    }
}
