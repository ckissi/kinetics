<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A search synonym for an effect, e.g. "snackbar" finding Toast Overshoot.
 */
class EffectKeyword extends Model
{
    public $timestamps = false;

    protected $fillable = ['effect_id', 'keyword'];

    public function effect(): BelongsTo
    {
        return $this->belongsTo(Effect::class);
    }
}
