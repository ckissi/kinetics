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

    /**
     * The opening of the prompt, shown to visitors who cannot read the rest.
     *
     * Prompts run from roughly 175 to 490 characters, so a fixed cut would
     * give away most of a short one and almost nothing of a long one. Taking a
     * share of the length and clamping it keeps the tease proportionate, and
     * the result is always cut at a word boundary.
     *
     * Only this string is ever sent to a visitor without access — the full
     * prompt does not reach the page at all, so there is nothing to recover
     * from the markup.
     */
    public function teaser(float $share = 0.4, int $min = 80, int $max = 160): string
    {
        $content = trim($this->content);
        $limit = (int) max($min, min($max, round(mb_strlen($content) * $share)));

        if (mb_strlen($content) <= $limit) {
            return $content;
        }

        $cut = mb_substr($content, 0, $limit);
        $lastSpace = mb_strrpos($cut, ' ');

        if ($lastSpace !== false && $lastSpace > $min * 0.5) {
            $cut = mb_substr($cut, 0, $lastSpace);
        }

        return rtrim($cut, " \t\n\r\0\x0B.,;:—-") . '…';
    }
}
