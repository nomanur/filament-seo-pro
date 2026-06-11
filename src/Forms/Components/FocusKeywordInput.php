<?php

declare(strict_types=1);

namespace Nomanur\FilamentSeoPro\Forms\Components;

use Filament\Forms\Components\TextInput;

/**
 * Focus keyword input field.
 *
 * The focus keyword drives the SEO analysis — all keyword-related
 * checks reference this value.
 */
class FocusKeywordInput extends TextInput
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('filament-seo-pro::seo.focus_keyword'))
            ->placeholder(__('filament-seo-pro::seo.focus_keyword_placeholder'))
            ->helperText(__('filament-seo-pro::seo.focus_keyword_helper'))
            ->prefixIcon('heroicon-o-key')
            ->maxLength(100)
            ->live(debounce: 500);
    }
}
