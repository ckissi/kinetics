<?php

namespace App\Models;

use App\Enums\AccessLevel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Effect extends Model
{
    protected $fillable = [
        'category_id',
        'slug',
        'name',
        'description',
        'param',
        'demo_html',
        'access_level',
        'position',
    ];

    protected function casts(): array
    {
        return ['access_level' => AccessLevel::class];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function snippets(): HasMany
    {
        return $this->hasMany(EffectSnippet::class);
    }

    public function prompts(): HasMany
    {
        return $this->hasMany(EffectPrompt::class);
    }

    public function keywords(): HasMany
    {
        return $this->hasMany(EffectKeyword::class);
    }

    public function scopeFree(Builder $query): Builder
    {
        return $query->where('access_level', AccessLevel::Free);
    }

    public function isPro(): bool
    {
        return $this->access_level->isPro();
    }
}
