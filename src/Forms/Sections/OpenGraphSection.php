<?php

declare(strict_types=1);

namespace Nomanur\FilamentSeoPro\Forms\Sections;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Section;

/**
 * Open Graph fields section with Facebook card preview.
 *
 * Provides og:title, og:description, og:image fields with a
 * live Facebook-style link share card preview.
 */
class OpenGraphSection extends Section
{
    public static function make(mixed $heading = null): static
    {
        $static = parent::make($heading ?? __('filament-seo-pro::seo.open_graph'));

        $static
            ->icon('heroicon-o-share')
            ->description(__('filament-seo-pro::seo.open_graph_description'))
            ->collapsible()
            ->collapsed()
            ->schema([
                TextInput::make('og_title')
                    ->label(__('filament-seo-pro::seo.og_title'))
                    ->placeholder(__('filament-seo-pro::seo.og_title_placeholder'))
                    ->maxLength(95)
                    ->live(debounce: 500)
                    ->hint(function (?string $state): string {
                        $length = mb_strlen($state ?? '');

                        return "{$length} / 95";
                    })
                    ->hintColor(function (?string $state): string {
                        $length = mb_strlen($state ?? '');

                        return match (true) {
                            $length === 0 => 'gray',
                            $length <= 60 => 'success',
                            $length <= 95 => 'warning',
                            default => 'danger',
                        };
                    }),

                Textarea::make('og_description')
                    ->label(__('filament-seo-pro::seo.og_description'))
                    ->placeholder(__('filament-seo-pro::seo.og_description_placeholder'))
                    ->rows(2)
                    ->maxLength(200)
                    ->live(debounce: 500),

                FileUpload::make('og_image')
                    ->label(__('filament-seo-pro::seo.og_image'))
                    ->image()
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('1.91:1')
                    ->imageResizeTargetWidth('1200')
                    ->imageResizeTargetHeight('630')
                    ->helperText(__('filament-seo-pro::seo.og_image_helper'))
                    ->directory('seo/og'),

                // Facebook Preview Card
                ViewField::make('og_preview')
                    ->view('filament-seo-pro::components.og-preview')
                    ->dehydrated(false)
                    ->label(__('filament-seo-pro::seo.og_preview')),
            ]);

        return $static;
    }
}
