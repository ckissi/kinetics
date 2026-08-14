<?php

namespace Tests\Feature;

use App\Enums\AccessLevel;
use App\Models\Effect;
use App\Models\EffectPrompt;
use Database\Seeders\EffectLibrarySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SyncProContentTest extends TestCase
{
    use RefreshDatabase;

    protected string $proPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(EffectLibrarySeeder::class);

        $this->proPath = base_path('tests/tmp/kinetics-pro');
        File::deleteDirectory($this->proPath);
        File::ensureDirectoryExists($this->proPath . '/effects');
    }

    protected function tearDown(): void
    {
        File::deleteDirectory(base_path('tests/tmp'));

        parent::tearDown();
    }

    protected function writePrompt(string $slug, string $type, string $content): void
    {
        $dir = $this->proPath . '/effects/' . $slug;
        File::ensureDirectoryExists($dir . '/prompts');
        File::put($dir . '/effect.json', json_encode(['slug' => $slug]));
        File::put($dir . '/prompts/' . $type . '.md', $content);
    }

    public function test_the_public_seed_ships_no_prompts(): void
    {
        $this->assertSame(0, EffectPrompt::count());
    }

    public function test_it_does_nothing_without_a_configured_path(): void
    {
        config(['kinetics.pro_path' => null]);

        $this->artisan('kinetics:sync-pro')
            ->expectsOutputToContain('nothing to sync')
            ->assertSuccessful();

        $this->assertSame(0, EffectPrompt::count());
    }

    public function test_it_imports_prompts_as_pro_content(): void
    {
        $this->writePrompt('magnetic-button', 'general', 'Pull the button toward the cursor.');

        $this->artisan('kinetics:sync-pro', ['--path' => $this->proPath])->assertSuccessful();

        $prompt = Effect::where('slug', 'magnetic-button')->firstOrFail()->prompts()->firstOrFail();

        $this->assertSame('general', $prompt->type);
        $this->assertSame('Pull the button toward the cursor.', $prompt->content);
        $this->assertSame(AccessLevel::Pro, $prompt->access_level);
    }

    public function test_it_imports_every_prompt_type_it_finds(): void
    {
        $this->writePrompt('magnetic-button', 'general', 'General prompt.');
        $this->writePrompt('magnetic-button', 'cursor', 'Cursor prompt.');
        $this->writePrompt('magnetic-button', 'claude', 'Claude prompt.');

        $this->artisan('kinetics:sync-pro', ['--path' => $this->proPath])->assertSuccessful();

        $this->assertEqualsCanonicalizing(
            ['claude', 'cursor', 'general'],
            Effect::where('slug', 'magnetic-button')->firstOrFail()->prompts->pluck('type')->all(),
        );
    }

    public function test_a_dry_run_writes_nothing(): void
    {
        $this->writePrompt('magnetic-button', 'general', 'Pull the button toward the cursor.');

        $this->artisan('kinetics:sync-pro', ['--path' => $this->proPath, '--dry-run' => true])
            ->assertSuccessful();

        $this->assertSame(0, EffectPrompt::count());
    }

    public function test_syncing_twice_changes_nothing(): void
    {
        $this->writePrompt('magnetic-button', 'general', 'Pull the button toward the cursor.');

        $this->artisan('kinetics:sync-pro', ['--path' => $this->proPath])->assertSuccessful();
        $first = EffectPrompt::firstOrFail();

        $this->artisan('kinetics:sync-pro', ['--path' => $this->proPath])->assertSuccessful();

        $this->assertSame(1, EffectPrompt::count());
        $this->assertSame($first->id, EffectPrompt::firstOrFail()->id);
    }

    public function test_it_updates_an_edited_prompt_in_place(): void
    {
        $this->writePrompt('magnetic-button', 'general', 'Original.');
        $this->artisan('kinetics:sync-pro', ['--path' => $this->proPath])->assertSuccessful();

        $this->writePrompt('magnetic-button', 'general', 'Revised.');
        $this->artisan('kinetics:sync-pro', ['--path' => $this->proPath])->assertSuccessful();

        $this->assertSame(1, EffectPrompt::count());
        $this->assertSame('Revised.', EffectPrompt::firstOrFail()->content);
    }

    public function test_it_fails_on_a_directory_matching_no_effect(): void
    {
        $this->writePrompt('no-such-effect', 'general', 'Orphan.');

        $this->artisan('kinetics:sync-pro', ['--path' => $this->proPath])
            ->assertFailed();

        $this->assertSame(0, EffectPrompt::count());
    }

    public function test_prune_removes_prompts_whose_files_are_gone(): void
    {
        $this->writePrompt('magnetic-button', 'general', 'Keep me.');
        $this->writePrompt('card-resize', 'general', 'Delete me.');
        $this->artisan('kinetics:sync-pro', ['--path' => $this->proPath])->assertSuccessful();
        $this->assertSame(2, EffectPrompt::count());

        File::deleteDirectory($this->proPath . '/effects/card-resize');

        $this->artisan('kinetics:sync-pro', ['--path' => $this->proPath, '--prune' => true])
            ->assertSuccessful();

        $this->assertSame(1, EffectPrompt::count());
        $this->assertSame('Keep me.', EffectPrompt::firstOrFail()->content);
    }

    public function test_without_prune_a_removed_file_leaves_its_prompt_alone(): void
    {
        $this->writePrompt('magnetic-button', 'general', 'Keep me.');
        $this->artisan('kinetics:sync-pro', ['--path' => $this->proPath])->assertSuccessful();

        File::deleteDirectory($this->proPath . '/effects/magnetic-button');

        $this->artisan('kinetics:sync-pro', ['--path' => $this->proPath])->assertSuccessful();

        $this->assertSame(1, EffectPrompt::count());
    }
}
