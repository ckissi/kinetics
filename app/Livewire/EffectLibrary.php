<?php

namespace App\Livewire;

use App\Models\Category;
use Illuminate\Support\Collection;
use Livewire\Component;

/**
 * The effect gallery.
 *
 * Rendering is entirely server-side today. Filtering still happens in
 * main.js against the DOM, which keeps search behaving exactly as it did
 * on the static site. The component exists as the seam for the paywall
 * and server-side filtering that the billing milestone introduces.
 */
class EffectLibrary extends Component
{
    public function render()
    {
        return view('livewire.effect-library', [
            'categories' => $this->categories(),
            'total' => $this->categories()->sum(fn(Category $c) => $c->effects->count()),
        ]);
    }

    protected function categories(): Collection
    {
        return once(fn() => Category::query()
            ->with([
                'effects' => fn($query) => $query->orderBy('position'),
                'effects.snippets',
                'effects.prompts',
            ])
            ->orderBy('position')
            ->get());
    }
}
