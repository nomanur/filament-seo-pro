<?php

declare(strict_types=1);

namespace Nomanur\FilamentSeoPro\Forms\Components;

use Filament\Forms\Components\Select;

/**
 * Schema.org type selector dropdown.
 *
 * Provides a pre-configured Select field with common Schema.org types.
 * JSON-LD generation will be added in future versions.
 */
class SchemaTypeSelect extends Select
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('filament-seo-pro::seo.schema_type'))
            ->placeholder(__('filament-seo-pro::seo.schema_type_placeholder'))
            ->options(config('filament-seo-pro.schema_types', []))
            ->searchable()
            ->helperText(__('filament-seo-pro::seo.schema_type_helper'));
    }
}
