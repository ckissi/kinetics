<?php

namespace Database\Seeders;

use App\Enums\AccessLevel;
use App\Models\Category;
use App\Models\Effect;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Seeds the free effect library from database/seeders/data/effects.json.
 *
 * That file was extracted once from the Astro site's body.html and is now the
 * source of truth for free content: the effects, their demo markup, their CSS
 * and React snippets and their search keywords.
 *
 * Prompts are deliberately absent. They are Pro content, they live in the
 * private kinetics-pro repository, and kinetics:sync-pro imports them.
 */
class EffectLibrarySeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/effects.json');

        if (! is_file($path)) {
            throw new RuntimeException("Effect data file is missing: {$path}");
        }

        $data = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        DB::transaction(function () use ($data) {
            $categories = [];

            foreach ($data['categories'] as $category) {
                $categories[$category['slug']] = Category::updateOrCreate(
                    ['slug' => $category['slug']],
                    [
                        'name' => $category['name'],
                        'position' => $category['position'],
                    ],
                );
            }

            foreach ($data['effects'] as $row) {
                $category = $categories[$row['category']]
                    ?? throw new RuntimeException("Unknown category '{$row['category']}' for effect '{$row['slug']}'");

                $effect = Effect::updateOrCreate(
                    ['slug' => $row['slug']],
                    [
                        'category_id' => $category->id,
                        'name' => $row['name'],
                        'description' => $row['description'],
                        'param' => $row['param'],
                        'demo_html' => $row['demo_html'],
                        'access_level' => AccessLevel::Free,
                        'position' => $row['position'],
                    ],
                );

                foreach (array_filter($row['snippets']) as $type => $content) {
                    $effect->snippets()->updateOrCreate(
                        ['type' => $type],
                        ['content' => $content, 'access_level' => AccessLevel::Free],
                    );
                }

                // Prompts are Pro content and live in the private repository.
                // kinetics:sync-pro imports them; nothing here touches them.

                $effect->keywords()->delete();
                $effect->keywords()->createMany(
                    array_map(fn(string $keyword) => ['keyword' => $keyword], $row['keywords'])
                );
            }
        });

        $this->command?->info(sprintf(
            'Seeded %d categories and %d effects.',
            count($data['categories']),
            count($data['effects']),
        ));
    }
}
