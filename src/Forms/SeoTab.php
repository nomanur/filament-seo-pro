<?php

declare(strict_types=1);

namespace Nomanur\FilamentSeoPro\Forms;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
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
 * Drop-in SEO tab for any Filament resource.
 *
 * Usage:
 *
 *     Tabs::make('Content')
 *         ->tabs([
 *             Tabs\Tab::make('Content')->schema([...]),
 *             SeoTab::make(),
 *         ])
 *
 * Configuration:
 *
 *     SeoTab::make()
 *         ->contentField('body')
 *         ->titleField('name')
 *         ->slugField('permalink')
 */
class SeoTab extends Tab
{
    protected string $contentField = 'content';

    protected string $titleField = 'title';

    protected string $slugField = 'slug';

    public static function make(mixed $label = null): static
    {
        $static = parent::make($label ?? __('filament-seo-pro::seo.tab_label'));

        $static->icon('heroicon-o-magnifying-glass');
        $static->badge(fn (): string => __('filament-seo-pro::seo.seo'));

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
            // Google Search Preview
            GooglePreview::make('seo_preview')
                ->dehydrated(false)
                ->titleField($this->titleField)
                ->slugField($this->slugField),

            // SEO Score + Checklist
            Grid::make(2)
                ->schema([
                    SeoScoreIndicator::make('seo_score_display')
                        ->dehydrated(false)
                        ->columnSpan(1),
                    SeoChecklist::make('seo_checklist_display')
                        ->dehydrated(false)
                        ->columnSpan(1),
                ]),

            Group::make([
                // Meta Settings Section
                Section::make(__('filament-seo-pro::seo.meta_settings'))
                    ->description(__('filament-seo-pro::seo.meta_settings_description'))
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->schema([
                        FocusKeywordInput::make('focus_keyword'),
                        SeoTitleInput::make('title'),
                        MetaDescriptionInput::make('description'),
                        TextInput::make('keywords')
                            ->label(__('filament-seo-pro::seo.keywords'))
                            ->placeholder(__('filament-seo-pro::seo.keywords_placeholder'))
                            ->helperText(__('filament-seo-pro::seo.keywords_helper'))
                            ->maxLength(255),
                        TextInput::make('canonical_url')
                            ->label(__('filament-seo-pro::seo.canonical_url'))
                            ->placeholder('https://example.com/page')
                            ->url()
                            ->maxLength(500),
                        Select::make('robots')
                            ->label(__('filament-seo-pro::seo.robots'))
                            ->options(config('filament-seo-pro.robots_options', []))
                            ->default('index, follow'),
                    ]),

                // Open Graph Section
                OpenGraphSection::make(),

                // Twitter Card Section
                TwitterCardSection::make(),

                // Schema Section
                SchemaSection::make(),
            ])->relationship('seo'),
        ];
    }
}
