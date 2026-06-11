<?php

declare(strict_types=1);

namespace Nomanur\FilamentSeoPro\Forms\Sections;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Section;

/**
 * Twitter Card fields section with card preview.
 *
 * Provides twitter:title, twitter:description, twitter:image fields
 * with a live Twitter Summary Large Image card preview.
 */
class TwitterCardSection extends Section
{
    public static function make(mixed $heading = null): static
    {
        $static = parent::make($heading ?? __('filament-seo-pro::seo.twitter_card'));

        $static
            ->icon('heroicon-o-chat-bubble-left-right')
            ->description(__('filament-seo-pro::seo.twitter_card_description'))
            ->collapsible()
            ->collapsed()
            ->schema([
                TextInput::make('seo.twitter_title')
                    ->label(__('filament-seo-pro::seo.twitter_title'))
                    ->placeholder(__('filament-seo-pro::seo.twitter_title_placeholder'))
                    ->maxLength(70)
                    ->live(debounce: 500)
                    ->hint(function (?string $state): string {
                        $length = mb_strlen($state ?? '');

                        return "{$length} / 70";
                    })
                    ->hintColor(function (?string $state): string {
                        $length = mb_strlen($state ?? '');

                        return match (true) {
                            $length === 0 => 'gray',
                            $length <= 55 => 'success',
                            $length <= 70 => 'warning',
                            default => 'danger',
                        };
                    }),

                Textarea::make('seo.twitter_description')
                    ->label(__('filament-seo-pro::seo.twitter_description'))
                    ->placeholder(__('filament-seo-pro::seo.twitter_description_placeholder'))
                    ->rows(2)
                    ->maxLength(200)
                    ->live(debounce: 500),

                FileUpload::make('seo.twitter_image')
                    ->label(__('filament-seo-pro::seo.twitter_image'))
                    ->image()
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('2:1')
                    ->imageResizeTargetWidth('1200')
                    ->imageResizeTargetHeight('600')
                    ->helperText(__('filament-seo-pro::seo.twitter_image_helper'))
                    ->directory('seo/twitter'),

                // Twitter Preview Card
                ViewField::make('twitter_preview')
                    ->view('filament-seo-pro::components.twitter-preview')
                    ->dehydrated(false)
                    ->label(__('filament-seo-pro::seo.twitter_preview')),
            ]);

        return $static;
    }
}
