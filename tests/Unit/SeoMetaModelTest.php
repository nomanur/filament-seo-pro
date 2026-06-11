<?php

declare(strict_types=1);

use Nomanur\FilamentSeoPro\Models\SeoMeta;

it('has the correct table name', function () {
    $model = new SeoMeta;

    expect($model->getTable())->toBe('seo_meta');
});

it('has the correct fillable attributes', function () {
    $model = new SeoMeta;

    expect($model->getFillable())->toContain('title');
    expect($model->getFillable())->toContain('description');
    expect($model->getFillable())->toContain('focus_keyword');
    expect($model->getFillable())->toContain('canonical_url');
    expect($model->getFillable())->toContain('robots');
    expect($model->getFillable())->toContain('og_title');
    expect($model->getFillable())->toContain('og_description');
    expect($model->getFillable())->toContain('og_image');
    expect($model->getFillable())->toContain('twitter_title');
    expect($model->getFillable())->toContain('twitter_description');
    expect($model->getFillable())->toContain('twitter_image');
    expect($model->getFillable())->toContain('schema_type');
    expect($model->getFillable())->toContain('seo_score');
});

it('casts seo_score to integer', function () {
    $model = new SeoMeta;
    $model->seo_score = '85';

    expect($model->seo_score)->toBeInt();
    expect($model->seo_score)->toBe(85);
});

it('can be created with mass assignment', function () {
    $meta = SeoMeta::create([
        'seoable_type' => 'App\\Models\\Post',
        'seoable_id' => 1,
        'title' => 'Test SEO Title',
        'description' => 'Test meta description for this page.',
        'focus_keyword' => 'test',
        'robots' => 'index, follow',
        'seo_score' => 75,
    ]);

    expect($meta)->toBeInstanceOf(SeoMeta::class);
    expect($meta->title)->toBe('Test SEO Title');
    expect($meta->seo_score)->toBe(75);
    expect($meta->exists)->toBeTrue();
});

it('has default robots value', function () {
    $meta = SeoMeta::create([
        'seoable_type' => 'App\\Models\\Post',
        'seoable_id' => 2,
    ]);

    expect($meta->robots)->toBe('index, follow');
});

it('has default seo_score of 0', function () {
    $meta = SeoMeta::create([
        'seoable_type' => 'App\\Models\\Post',
        'seoable_id' => 3,
    ]);

    expect($meta->seo_score)->toBe(0);
});
