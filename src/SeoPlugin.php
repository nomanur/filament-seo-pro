<?php

declare(strict_types=1);

namespace Nomanur\FilamentSeoPro;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Nomanur\FilamentSeoPro\Pages\SeoManagement;
use Nomanur\FilamentSeoPro\Widgets\SeoOverviewWidget;

/**
 * Filament SEO Pro Plugin — the definitive SEO toolkit for Filament.
 *
 * Register in your PanelProvider:
 *
 *     ->plugins([
 *         SeoPlugin::make(),
 *     ])
 *
 * Configure per-panel:
 *
 *     SeoPlugin::make()
 *         ->defaultContentField('body')
 *         ->defaultTitleField('name')
 *         ->enableDashboardWidget()
 *         ->enableManagementPage()
 */
class SeoPlugin implements Plugin
{
    protected string $defaultContentField = 'content';

    protected string $defaultTitleField = 'title';

    protected string $defaultSlugField = 'slug';

    protected bool $dashboardWidgetEnabled = true;

    protected bool $managementPageEnabled = true;

    protected bool $translatableEnabled = false;

    /**
     * @var list<class-string>
     */
    protected array $models = [];

    /**
     * Get the unique plugin identifier.
     */
    public function getId(): string
    {
        return 'seo-pro';
    }

    /**
     * Register plugin resources, pages, and widgets with the panel.
     */
    public function register(Panel $panel): void
    {
        if ($this->dashboardWidgetEnabled) {
            $panel->widgets([
                SeoOverviewWidget::class,
            ]);
        }

        if ($this->managementPageEnabled) {
            $panel->pages([
                SeoManagement::class,
            ]);
        }
    }

    /**
     * Boot the plugin when the panel is in use.
     */
    public function boot(Panel $panel): void
    {
        // Apply plugin configuration to the global config at runtime
        config([
            'filament-seo-pro.default_content_field' => $this->defaultContentField,
            'filament-seo-pro.default_title_field' => $this->defaultTitleField,
            'filament-seo-pro.default_slug_field' => $this->defaultSlugField,
            'filament-seo-pro.translatable' => $this->translatableEnabled,
        ]);

        if (! empty($this->models)) {
            config(['filament-seo-pro.models' => $this->models]);
        }
    }

    /**
     * Create a new plugin instance.
     */
    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * Retrieve the plugin instance from the current panel.
     */
    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    /**
     * Set the default content field name used for SEO analysis.
     */
    public function defaultContentField(string $field): static
    {
        $this->defaultContentField = $field;

        return $this;
    }

    /**
     * Set the default title field name used for SEO analysis.
     */
    public function defaultTitleField(string $field): static
    {
        $this->defaultTitleField = $field;

        return $this;
    }

    /**
     * Set the default slug field name used for SEO analysis.
     */
    public function defaultSlugField(string $field): static
    {
        $this->defaultSlugField = $field;

        return $this;
    }

    /**
     * Enable or disable the dashboard overview widget.
     */
    public function enableDashboardWidget(bool $enabled = true): static
    {
        $this->dashboardWidgetEnabled = $enabled;

        return $this;
    }

    /**
     * Enable or disable the bulk SEO management page.
     */
    public function enableManagementPage(bool $enabled = true): static
    {
        $this->managementPageEnabled = $enabled;

        return $this;
    }

    /**
     * Enable support for spatie/laravel-translatable models.
     */
    public function translatable(bool $enabled = true): static
    {
        $this->translatableEnabled = $enabled;

        return $this;
    }

    /**
     * Register models for bulk SEO management.
     *
     * @param  list<class-string>  $models
     */
    public function models(array $models): static
    {
        $this->models = $models;

        return $this;
    }

    /**
     * Getters for plugin state (used by components).
     */
    public function getDefaultContentField(): string
    {
        return $this->defaultContentField;
    }

    public function getDefaultTitleField(): string
    {
        return $this->defaultTitleField;
    }

    public function getDefaultSlugField(): string
    {
        return $this->defaultSlugField;
    }

    public function isDashboardWidgetEnabled(): bool
    {
        return $this->dashboardWidgetEnabled;
    }

    public function isManagementPageEnabled(): bool
    {
        return $this->managementPageEnabled;
    }

    public function isTranslatableEnabled(): bool
    {
        return $this->translatableEnabled;
    }

    /**
     * @return list<class-string>
     */
    public function getModels(): array
    {
        return $this->models;
    }
}
