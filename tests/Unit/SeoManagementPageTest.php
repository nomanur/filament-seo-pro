<?php

declare(strict_types=1);

use Nomanur\FilamentSeoPro\Pages\SeoManagement;
use Nomanur\FilamentSeoPro\Models\SeoMeta;

it('calculates page stats correctly on SeoManagement page', function () {
    // Populate database with some records
    SeoMeta::create([
        'seoable_type' => 'App\\Models\\Post',
        'seoable_id' => 1,
        'title' => 'Title 1',
        'description' => 'Description 1',
        'seo_score' => 85,
    ]);

    SeoMeta::create([
        'seoable_type' => 'App\\Models\\Post',
        'seoable_id' => 2,
        'title' => '',
        'description' => '',
        'seo_score' => 20,
    ]);

    $page = new SeoManagement();

    expect($page->getAverageScore())->toBe(53); // (85 + 20) / 2 = 52.5 -> 53
    expect($page->getMissingTitlesCount())->toBe(1);
    expect($page->getMissingDescriptionsCount())->toBe(1);
    expect($page->getHealthyPercentage())->toBe(50); // 1 out of 2 is > 60
    expect($page->getLowestScoring(2))->toHaveCount(2);
    expect($page->getLowestScoring(2)->first()->seo_score)->toBe(20);
    expect($page->getColorForScore(85))->toBe('success');
    expect($page->getColorForScore(20))->toBe('danger');
});
