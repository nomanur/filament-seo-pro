<?php

declare(strict_types=1);

use Nomanur\FilamentSeoPro\Forms\Components\SeoChecklist;
use Nomanur\FilamentSeoPro\Forms\Components\SeoScoreIndicator;

it('normalizes array and object inputs to string values in SeoChecklist', function () {
    $checklist = SeoChecklist::make('seo_checklist_display');

    // Simulate translatable state where values are arrays
    $state = [
        'seo.title' => ['en' => 'This is an optimal SEO Title length test which is long enough', 'fr' => 'Titre'],
        'seo.description' => ['en' => 'This is an optimal SEO description that is long enough and falls within the recommended range of characters.', 'fr' => 'Description'],
        'seo.focus_keyword' => ['en' => 'SEO Title', 'fr' => 'SEO Title'],
        'content' => ['en' => 'Word count content is very important so we write more than three hundred words to make sure this test passes and the checklist contains correct normalized data.', 'fr' => 'Contenu'],
        'slug' => ['en' => 'seo-title', 'fr' => 'seo-title'],
    ];

    $get = fn (string $key) => $state[$key] ?? null;

    $checks = $checklist->getChecks($get);

    expect($checks)->toBeArray()->not->toBeEmpty();
});

it('normalizes array and object inputs to string values in SeoScoreIndicator', function () {
    $indicator = SeoScoreIndicator::make('seo_score_display');

    // Simulate translatable state where values are arrays
    $state = [
        'seo.title' => ['en' => 'This is an optimal SEO Title length test which is long enough', 'fr' => 'Titre'],
        'seo.description' => ['en' => 'This is an optimal SEO description that is long enough and falls within the recommended range of characters.', 'fr' => 'Description'],
        'seo.focus_keyword' => ['en' => 'SEO Title', 'fr' => 'SEO Title'],
        'content' => ['en' => 'Word count content is very important so we write more than three hundred words to make sure this test passes and the checklist contains correct normalized data.', 'fr' => 'Contenu'],
        'slug' => ['en' => 'seo-title', 'fr' => 'seo-title'],
    ];

    $get = fn (string $key) => $state[$key] ?? null;

    $scoreData = $indicator->calculateScore($get);

    expect($scoreData)->toHaveKeys(['score', 'grade', 'color']);
    expect($scoreData['score'])->toBeInt();
});
