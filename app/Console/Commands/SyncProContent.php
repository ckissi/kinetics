<?php

namespace App\Console\Commands;

use App\Enums\AccessLevel;
use App\Models\Effect;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * Imports paid content from the private kinetics-pro repository.
 *
 * Deployment clones that repository outside the web root, points
 * KINETICS_PRO_PATH at it, and runs this command. With the variable unset the
 * command does nothing, so the public repository stands alone.
 */
class SyncProContent extends Command
{
    protected $signature = 'kinetics:sync-pro
                            {--path= : Override KINETICS_PRO_PATH}
                            {--dry-run : Report what would change without writing}
                            {--prune : Delete Pro prompts that no longer exist in the repository}';

    protected $description = 'Import Pro content from the private kinetics-pro repository';

    public function handle(): int
    {
        $path = $this->option('path') ?: config('kinetics.pro_path');

        if (blank($path)) {
            $this->components->info('KINETICS_PRO_PATH is not set — nothing to sync.');

            return self::SUCCESS;
        }

        $path = rtrim($path, '/');
        $effectsDir = $path . '/effects';

        if (! is_dir($effectsDir)) {
            $this->components->error("No effects directory at {$effectsDir}");

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');

        // Slug => id, so an unknown directory is reported rather than silently
        // creating an effect with no demo, CSS or category.
        $effectIds = Effect::query()->pluck('id', 'slug');

        $created = 0;
        $updated = 0;
        $unchanged = 0;
        $unknown = [];
        $seen = [];

        $directories = Finder::create()->directories()->in($effectsDir)->depth(0)->sortByName();

        foreach ($directories as $directory) {
            $slug = $this->slugFor($directory);

            if (! $effectIds->has($slug)) {
                $unknown[] = $slug;
                continue;
            }

            $effectId = $effectIds->get($slug);
            $promptsDir = $directory->getRealPath() . '/prompts';

            if (! is_dir($promptsDir)) {
                continue;
            }

            $files = Finder::create()->files()->in($promptsDir)->name('*.md')->depth(0)->sortByName();

            foreach ($files as $file) {
                $type = $file->getBasename('.md');
                $content = rtrim(file_get_contents($file->getRealPath()));

                if (blank($content)) {
                    $this->components->warn("Skipping empty prompt: {$slug}/{$type}.md");
                    continue;
                }

                $seen[] = $effectId . ':' . $type;

                $existing = DB::table('effect_prompts')
                    ->where('effect_id', $effectId)
                    ->where('type', $type)
                    ->first();

                if ($existing === null) {
                    $created++;
                } elseif ($existing->content !== $content || $existing->access_level !== AccessLevel::Pro->value) {
                    $updated++;
                } else {
                    $unchanged++;
                    continue;
                }

                if ($dryRun) {
                    continue;
                }

                Effect::find($effectId)->prompts()->updateOrCreate(
                    ['type' => $type],
                    ['content' => $content, 'access_level' => AccessLevel::Pro],
                );
            }
        }

        $pruned = $this->prune($seen, $dryRun);

        $this->report($created, $updated, $unchanged, $pruned, $unknown, $dryRun);

        return $unknown === [] ? self::SUCCESS : self::FAILURE;
    }

    /**
     * effect.json is authoritative; the directory name is the fallback.
     */
    protected function slugFor(SplFileInfo $directory): string
    {
        $metaPath = $directory->getRealPath() . '/effect.json';

        if (is_file($metaPath)) {
            $meta = json_decode(file_get_contents($metaPath), true);

            if (is_array($meta) && filled($meta['slug'] ?? null)) {
                return $meta['slug'];
            }
        }

        return $directory->getBasename();
    }

    /**
     * Remove Pro prompts whose files have been deleted from the repository.
     * Free prompts, if any ever exist, are left alone.
     */
    protected function prune(array $seen, bool $dryRun): int
    {
        if (! $this->option('prune')) {
            return 0;
        }

        $stale = DB::table('effect_prompts')
            ->where('access_level', AccessLevel::Pro->value)
            ->get(['id', 'effect_id', 'type'])
            ->reject(fn($row) => in_array($row->effect_id . ':' . $row->type, $seen, true));

        if (! $dryRun && $stale->isNotEmpty()) {
            DB::table('effect_prompts')->whereIn('id', $stale->pluck('id'))->delete();
        }

        return $stale->count();
    }

    protected function report(int $created, int $updated, int $unchanged, int $pruned, array $unknown, bool $dryRun): void
    {
        if ($dryRun) {
            $this->components->info('Dry run — nothing was written.');
        }

        $this->components->twoColumnDetail('Prompts created', (string) $created);
        $this->components->twoColumnDetail('Prompts updated', (string) $updated);
        $this->components->twoColumnDetail('Prompts unchanged', (string) $unchanged);

        if ($this->option('prune')) {
            $this->components->twoColumnDetail('Prompts pruned', (string) $pruned);
        }

        if ($unknown !== []) {
            $this->newLine();
            $this->components->error(sprintf(
                '%s in the Pro repository %s no effect: %s',
                count($unknown) === 1 ? '1 directory' : count($unknown) . ' directories',
                count($unknown) === 1 ? 'matches' : 'match',
                implode(', ', $unknown),
            ));
            $this->line('  Seed the public library first, or correct the slug in effect.json.');
        }
    }
}
