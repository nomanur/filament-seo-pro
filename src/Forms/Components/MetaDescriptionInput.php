<?php

declare(strict_types=1);

namespace Nomanur\FilamentSeoPro\Forms\Components;

use Filament\Forms\Components\Textarea;

/**
 * Meta Description input with character counter and color-coded validation.
 *
 * Displays a character count indicator that changes color based on optimal
 * description length (120-160 characters for Google).
 */
class MetaDescriptionInput extends Textarea
{
    protected function setUp(): void
    {
        parent::setUp();

        $minLength = (int) config('filament-seo-pro.description_length.min', 120);
        $maxLength = (int) config('filament-seo-pro.description_length.max', 160);

        $this
            ->label(__('filament-seo-pro::seo.meta_description'))
            ->placeholder(__('filament-seo-pro::seo.meta_description_placeholder'))
            ->rows(3)
            ->maxLength(200)
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
                    return __('filament-seo-pro::seo.description_helper_empty');
                }

                if ($length < $minLength) {
                    return __('filament-seo-pro::seo.description_helper_short', [
                        'min' => $minLength,
                        'current' => $length,
                    ]);
                }

                if ($length > $maxLength) {
                    return __('filament-seo-pro::seo.description_helper_long', [
                        'max' => $maxLength,
                        'current' => $length,
                    ]);
                }

                return __('filament-seo-pro::seo.description_helper_optimal');
            });
    }
}
