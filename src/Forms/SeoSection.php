<?php

declare(strict_types=1);

namespace Nomanur\FilamentSeoPro\Forms;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Nomanur\FilamentSeoPro\Forms\Components\FocusKeywordInput;
use Nomanur\FilamentSeoPro\Forms\Components\GooglePreview;
use Nomanur\FilamentSeoPro\Forms\Components\MetaDescriptionInput;
use Nomanur\FilamentSeoPro\Forms\Components\SeoChecklist;
use Nomanur\FilamentSeoPro\Forms\Components\SeoScoreIndicator;
use Nomanur\FilamentSeoPro\Forms\Components\SeoTitleInput;
use Nomanur\FilamentSeoPro\Forms\Sections\OpenGraphSection;
use Nomanur\FilamentSeoPro\Forms\Sections\SchemaSection;
use Nomanur\FilamentSeoPro\Forms\Sections\TwitterCardSection;

/**
 * Drop-in SEO section for non-tab layouts.
 *
 * Usage:
 *
 *     Forms\Components\Group::make([
 *         // ...other fields
 *         SeoSection::make(),
 *     ])
 *
 * Supports the same fluent configuration as SeoTab:
 *
 *     SeoSection::make()
 *         ->contentField('body')
 *         ->titleField('name')
 *         ->slugField('permalink')
 */
class SeoSection extends Section
{
    protected string $contentField = 'content';

    protected string $titleField = 'title';

    protected string $slugField = 'slug';

    public static function make(mixed $heading = null): static
    {
        $static = parent::make($heading ?? __('filament-seo-pro::seo.section_label'));

        $static->icon('heroicon-o-magnifying-glass');
        $static->description(__('filament-seo-pro::seo.section_description'));
        $static->collapsible();
        $static->collapsed();

        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        $this->schema($this->buildSchema());
    }

    /**
     * Set the content field name used for analysis.
     */
    public function contentField(string $field): static
    {
        $this->contentField = $field;

        return $this->schema($this->buildSchema());
    }

    /**
     * Set the title field name used for analysis.
     */
    public function titleField(string $field): static
    {
        $this->titleField = $field;

        return $this->schema($this->buildSchema());
    }

    /**
     * Set the slug field name used for analysis.
     */
    public function slugField(string $field): static
    {
        $this->slugField = $field;

        return $this->schema($this->buildSchema());
    }

    /**
     * Build the complete SEO form schema.
     *
     * @return array<int, Component>
     */
    protected function buildSchema(): array
    {
        return [
            GooglePreview::make('seo_preview')
                ->titleField($this->titleField)
                ->slugField($this->slugField),

            Grid::make(2)
                ->schema([
                    SeoScoreIndicator::make('seo_score_display')
                        ->columnSpan(1),
                    SeoChecklist::make('seo_checklist_display')
                        ->columnSpan(1),
                ]),

            Section::make(__('filament-seo-pro::seo.meta_settings'))
                ->icon('heroicon-o-document-text')
                ->collapsible()
                ->schema([
                    FocusKeywordInput::make('seo.focus_keyword'),
                    SeoTitleInput::make('seo.title'),
                    MetaDescriptionInput::make('seo.description'),
                    TextInput::make('seo.keywords')
                        ->label(__('filament-seo-pro::seo.keywords'))
                        ->placeholder(__('filament-seo-pro::seo.keywords_placeholder'))
                        ->maxLength(255),
                    TextInput::make('seo.canonical_url')
                        ->label(__('filament-seo-pro::seo.canonical_url'))
                        ->url()
                        ->maxLength(500),
                    Select::make('seo.robots')
                        ->label(__('filament-seo-pro::seo.robots'))
                        ->options(config('filament-seo-pro.robots_options', []))
                        ->default('index, follow'),
                ]),

            OpenGraphSection::make(),
            TwitterCardSection::make(),
            SchemaSection::make(),
        ];
    }
}
