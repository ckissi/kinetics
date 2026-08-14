<?php

namespace App\Enums;

/**
 * Who may see a piece of content.
 *
 * This lives on effects, snippets and prompts alike, so an effect can be
 * free to look at while some of its snippets stay behind the paywall.
 */
enum AccessLevel: string
{
    case Free = 'free';
    case Pro = 'pro';

    public function isPro(): bool
    {
        return $this === self::Pro;
    }
}
