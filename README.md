# Filament SEO Pro

[![Latest Version on Packagist](https://img.shields.io/packagist/v/nomanur/filament-seo-pro.svg?style=flat-square)](https://packagist.org/packages/nomanur/filament-seo-pro)
[![Total Downloads](https://img.shields.io/packagist/dt/nomanur/filament-seo-pro.svg?style=flat-square)](https://packagist.org/packages/nomanur/filament-seo-pro)
[![GitHub Actions](https://github.com/nomanur/filament-seo-pro/actions/workflows/main.yml/badge.svg)](https://github.com/nomanur/filament-seo-pro/actions)

The definitive SEO toolkit for Filament v4.

For full developer documentation, guides, and interactive configurations, visit [nomanur.github.io/filament-seo-pro](https://nomanur.github.io/filament-seo-pro/).

Filament SEO Pro brings a complete Yoast-like SEO experience directly into your Filament panels. It provides live analysis of your content as you type, Google and social media preview cards, Schema.org type selection, a dashboard widget, and a bulk SEO management page.

## Features

- 🟢 **Live SEO Analysis** — 13 comprehensive checks for titles, descriptions, content length, keywords, headings, and links.
- 📖 **Readability Analysis** — Scores your content based on sentence length, paragraph structure, passive voice, and transition words.
- 🔍 **Google Search Preview** — See exactly how your content will look in Google search results, updated in real-time.
- 🔗 **Social Media Previews** — Live preview cards for Open Graph (Facebook) and Twitter.
- 🎯 **Dashboard Widget** — Overview of your site's SEO health with average scores and missing meta tags.
- 📊 **Bulk Management Page** — View, filter, analyze, and export the SEO status of all your content from one place.

## Installation

You can install the package via composer:

```bash
composer require nomanur/filament-seo-pro
```

Publish the assets:

```bash
php artisan filament:assets
```

Publish and run the migrations:

```bash
php artisan vendor:publish --tag="filament-seo-pro-migrations"
php artisan migrate
```

You can optionally publish the config file:

```bash
php artisan vendor:publish --tag="filament-seo-pro-config"
```

## Setup

### 1. Register the Plugin

Add `SeoPlugin::make()` to your panel provider (e.g., `app/Providers/Filament/AdminPanelProvider.php`):

```php
use Nomanur\FilamentSeoPro\SeoPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->plugins([
            SeoPlugin::make(),
        ]);
}
```

### 2. Prepare your Models

Add the `HasSeo` trait to any models that require SEO metadata:

```php
use Illuminate\Database\Eloquent\Model;
use Nomanur\FilamentSeoPro\Traits\HasSeo;

class Post extends Model
{
    use HasSeo;
    // ...
}
```

### 3. Add to your Resources

Add `SeoTab::make()` to your resource's form schema. For example, within a `Tabs` component:

```php
use Filament\Forms\Components\Tabs;
use Nomanur\FilamentSeoPro\Forms\SeoTab;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Tabs::make('Post')
                ->tabs([
                    Tabs\Tab::make('Content')
                        ->schema([
                            // Your content fields
                        ]),
                    SeoTab::make(), // Automatically injects the full SEO interface
                ])
        ]);
}
```

If you aren't using tabs, you can use `SeoSection::make()` instead:

```php
use Nomanur\FilamentSeoPro\Forms\SeoSection;

public static function form(Form $form): Form
{
    return $form
        ->schema([
            // Your content fields...
            SeoSection::make(),
        ]);
}
```

## Configuration

### Plugin Configuration

You can configure the plugin directly when registering it in your panel provider:

```php
SeoPlugin::make()
    ->defaultContentField('body') // Field used for content analysis (default: 'content')
    ->defaultTitleField('name')   // Field used as fallback title (default: 'title')
    ->defaultSlugField('permalink') // Field used for URL preview (default: 'slug')
    ->enableDashboardWidget(true) // Show SEO Overview widget on dashboard
    ->enableManagementPage(true)  // Show SEO Management page in navigation
    ->models([                    // Models to include in the SEO Management page
        \App\Models\Post::class,
        \App\Models\Page::class,
    ]);
```

### Tab Configuration

You can override the default fields on a per-resource basis:

```php
SeoTab::make()
    ->contentField('post_body')
    ->titleField('post_title')
    ->slugField('post_slug')
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email nomanurrahman@gmail.com instead of using the issue tracker.

## Credits

- [Nomanur Rahman](https://github.com/nomanur)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
