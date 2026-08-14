<?php

namespace App\Models;

use App\Enums\AccessLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A copyable code sample for an effect: the CSS or the React component.
 */
class EffectSnippet extends Model
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
