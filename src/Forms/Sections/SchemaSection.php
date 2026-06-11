<?php

declare(strict_types=1);

namespace Nomanur\FilamentSeoPro\Forms\Sections;

use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Nomanur\FilamentSeoPro\Forms\Components\SchemaTypeSelect;

/**
 * Schema.org section for structured data type selection.
 *
 * Allows the user to choose a Schema.org type for this content.
 * JSON-LD generation will be added in future versions.
 */
class SchemaSection extends Section
{
    public static function make(mixed $heading = null): static
    {
        $static = parent::make($heading ?? __('filament-seo-pro::seo.schema_markup'));

        $static
            ->icon('heroicon-o-code-bracket')
            ->description(__('filament-seo-pro::seo.schema_markup_description'))
            ->collapsible()
            ->collapsed()
            ->schema([
                SchemaTypeSelect::make('seo.schema_type'),

                Placeholder::make('schema_info')
                    ->content(__('filament-seo-pro::seo.schema_info'))
                    ->helperText(__('filament-seo-pro::seo.schema_info_helper')),
            ]);

        return $static;
    }
}
