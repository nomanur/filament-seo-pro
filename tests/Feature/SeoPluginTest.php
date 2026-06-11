<?php

declare(strict_types=1);

use Nomanur\FilamentSeoPro\SeoPlugin;

it('has the correct plugin id', function () {
    $plugin = SeoPlugin::make();

    expect($plugin->getId())->toBe('seo-pro');
});

it('has default field configuration', function () {
    $plugin = SeoPlugin::make();

    expect($plugin->getDefaultContentField())->toBe('content');
    expect($plugin->getDefaultTitleField())->toBe('title');
    expect($plugin->getDefaultSlugField())->toBe('slug');
});

it('can configure content field', function () {
    $plugin = SeoPlugin::make()->defaultContentField('body');

    expect($plugin->getDefaultContentField())->toBe('body');
});

it('can configure title field', function () {
    $plugin = SeoPlugin::make()->defaultTitleField('name');

    expect($plugin->getDefaultTitleField())->toBe('name');
});

it('can configure slug field', function () {
    $plugin = SeoPlugin::make()->defaultSlugField('permalink');

    expect($plugin->getDefaultSlugField())->toBe('permalink');
});

it('has dashboard widget enabled by default', function () {
    $plugin = SeoPlugin::make();

    expect($plugin->isDashboardWidgetEnabled())->toBeTrue();
});

it('can disable dashboard widget', function () {
    $plugin = SeoPlugin::make()->enableDashboardWidget(false);

    expect($plugin->isDashboardWidgetEnabled())->toBeFalse();
});

it('has management page enabled by default', function () {
    $plugin = SeoPlugin::make();

    expect($plugin->isManagementPageEnabled())->toBeTrue();
});

it('can disable management page', function () {
    $plugin = SeoPlugin::make()->enableManagementPage(false);

    expect($plugin->isManagementPageEnabled())->toBeFalse();
});

it('has translatable disabled by default', function () {
    $plugin = SeoPlugin::make();

    expect($plugin->isTranslatableEnabled())->toBeFalse();
});

it('can enable translatable', function () {
    $plugin = SeoPlugin::make()->translatable();

    expect($plugin->isTranslatableEnabled())->toBeTrue();
});

it('can register models', function () {
    $plugin = SeoPlugin::make()->models([
        'App\\Models\\Post',
        'App\\Models\\Page',
    ]);

    expect($plugin->getModels())->toHaveCount(2);
    expect($plugin->getModels())->toContain('App\\Models\\Post');
});

it('supports fluent configuration chaining', function () {
    $plugin = SeoPlugin::make()
        ->defaultContentField('body')
        ->defaultTitleField('name')
        ->defaultSlugField('permalink')
        ->enableDashboardWidget(false)
        ->enableManagementPage(false)
        ->translatable()
        ->models(['App\\Models\\Post']);

    expect($plugin->getDefaultContentField())->toBe('body');
    expect($plugin->getDefaultTitleField())->toBe('name');
    expect($plugin->getDefaultSlugField())->toBe('permalink');
    expect($plugin->isDashboardWidgetEnabled())->toBeFalse();
    expect($plugin->isManagementPageEnabled())->toBeFalse();
    expect($plugin->isTranslatableEnabled())->toBeTrue();
    expect($plugin->getModels())->toHaveCount(1);
});
