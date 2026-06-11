<?php

declare(strict_types=1);

namespace Nomanur\FilamentSeoPro;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Livewire\Livewire;
use Nomanur\FilamentSeoPro\Livewire\SeoAnalyzerPanel;
use Nomanur\FilamentSeoPro\Services\ReadabilityAnalyzer;
use Nomanur\FilamentSeoPro\Services\SeoAnalyzer;
use Nomanur\FilamentSeoPro\Services\SeoScoreCalculator;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SeoServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-seo-pro';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_seo_meta_table')
            ->hasTranslations();
    }

    public function packageRegistered(): void
    {
        // Register analysis services as singletons
        $this->app->singleton(SeoAnalyzer::class);
        $this->app->singleton(SeoScoreCalculator::class);
        $this->app->singleton(ReadabilityAnalyzer::class);
    }

    public function packageBooted(): void
    {
        // Register Filament CSS assets
        FilamentAsset::register([
            Css::make('filament-seo-pro', __DIR__ . '/../resources/css/seo-pro.css'),
        ], package: 'nomanurrahman/filament-seo-pro');

        // Register Livewire components
        Livewire::component('seo-analyzer-panel', SeoAnalyzerPanel::class);
    }
}
