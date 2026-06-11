<?php

declare(strict_types=1);

namespace Nomanur\FilamentSeoPro\Forms\Components;

use Filament\Schemas\Components\Component;

/**
 * Live Google Search Preview component.
 *
 * Displays a realistic Google SERP preview that updates reactively
 * as the user edits the SEO title, meta description, and slug fields.
 */
class GooglePreview extends Component
{
    protected string $view = 'filament-seo-pro::components.google-preview';

    protected string $seoTitleField = 'seo.title';

    protected string $seoDescriptionField = 'seo.description';

    protected string $modelTitleField = 'title';

    protected string $modelSlugField = 'slug';

    public static function make(string $name = 'google_preview'): static
    {
        $static = app(static::class);

        $static->statePath($name);

        return $static;
    }

    /**
     * Set the model's title field name (used as fallback for SEO title).
     */
    public function titleField(string $field): static
    {
        $this->modelTitleField = $field;

        return $this;
    }

    /**
     * Set the model's slug field name (used for URL preview).
     */
    public function slugField(string $field): static
    {
        $this->modelSlugField = $field;

        return $this;
    }

    public function getSeoTitleField(): string
    {
        return $this->seoTitleField;
    }

    public function getSeoDescriptionField(): string
    {
        return $this->seoDescriptionField;
    }

    public function getModelTitleField(): string
    {
        return $this->modelTitleField;
    }

    public function getModelSlugField(): string
    {
        return $this->modelSlugField;
    }
}
