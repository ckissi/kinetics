<?php

namespace App\Models;

use App\Enums\AccessLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * An LLM prompt that recreates the effect. The library ships a general
 * prompt today; Cursor and Claude variants arrive with the Pro content.
 */
class EffectPrompt extends Model
{
    protected $fillable = ['effect_id', 'type', 'content', 'access_level'];

    protected function casts(): array
    {
        return ['access_level' => AccessLevel::class];
    }

    public function effect(): BelongsTo
    {
        return $this->belongsTo(Effect::class);
    }

    public function isPro(): bool
    {
        return $this->access_level->isPro();
    }
}
