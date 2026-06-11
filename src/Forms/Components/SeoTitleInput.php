<?php

declare(strict_types=1);

namespace Nomanur\FilamentSeoPro\Forms\Components;

use Filament\Forms\Components\TextInput;

/**
 * SEO Title input with character counter and color-coded validation.
 *
 * Displays a character count indicator that changes color based on optimal
 * title length (50-60 characters for Google).
 */
class SeoTitleInput extends TextInput
{
    protected function setUp(): void
    {
        parent::setUp();

        $minLength = (int) config('filament-seo-pro.title_length.min', 50);
        $maxLength = (int) config('filament-seo-pro.title_length.max', 60);

        $this
            ->label(__('filament-seo-pro::seo.seo_title'))
            ->placeholder(__('filament-seo-pro::seo.seo_title_placeholder'))
            ->maxLength(70)
            ->live(debounce: 500)
            ->hint(function (?string $state) use ($maxLength): string {
                $length = mb_strlen($state ?? '');

                return "{$length} / {$maxLength}";
            })
            ->hintColor(function (?string $state) use ($minLength, $maxLength): string {
                $length = mb_strlen($state ?? '');

                if ($length === 0) {
                    return 'gray';
                }

                if ($length >= $minLength && $length <= $maxLength) {
                    return 'success';
                }

                if ($length > $maxLength) {
                    return 'danger';
                }

                return 'warning';
            })
            ->hintIcon('heroicon-o-information-circle')
            ->helperText(function (?string $state) use ($minLength, $maxLength): string {
                $length = mb_strlen($state ?? '');

                if ($length === 0) {
                    return __('filament-seo-pro::seo.title_helper_empty');
                }

                if ($length < $minLength) {
                    return __('filament-seo-pro::seo.title_helper_short', [
                        'min' => $minLength,
                        'current' => $length,
                    ]);
                }

                if ($length > $maxLength) {
                    return __('filament-seo-pro::seo.title_helper_long', [
                        'max' => $maxLength,
                        'current' => $length,
                    ]);
                }

                return __('filament-seo-pro::seo.title_helper_optimal');
            });
    }
}
