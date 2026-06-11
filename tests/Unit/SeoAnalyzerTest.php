<?php

declare(strict_types=1);

use Nomanur\FilamentSeoPro\DTOs\SeoAnalysisResult;
use Nomanur\FilamentSeoPro\Enums\CheckStatus;
use Nomanur\FilamentSeoPro\Services\SeoAnalyzer;

beforeEach(function () {
    $this->analyzer = app(SeoAnalyzer::class);
});

it('returns an SeoAnalysisResult', function () {
    $result = $this->analyzer->analyze([
        'title' => 'Test Title',
        'description' => 'Test description',
        'focus_keyword' => 'test',
        'content' => '<p>Some content here</p>',
        'slug' => 'test-title',
        'url' => 'https://example.com/test-title',
    ]);

    expect($result)->toBeInstanceOf(SeoAnalysisResult::class);
    expect($result->checks)->toBeArray();
    expect($result->checks)->not->toBeEmpty();
});

it('passes title exists check when title is provided', function () {
    $result = $this->analyzer->analyze([
        'title' => 'My SEO Title',
        'description' => '',
        'focus_keyword' => '',
        'content' => '',
        'slug' => '',
        'url' => '',
    ]);

    $check = collect($result->checks)->firstWhere('key', 'title_exists');
    expect($check)->not->toBeNull();
    expect($check->status)->toBe(CheckStatus::Pass);
});

it('fails title exists check when title is empty', function () {
    $result = $this->analyzer->analyze([
        'title' => '',
        'description' => '',
        'focus_keyword' => '',
        'content' => '',
        'slug' => '',
        'url' => '',
    ]);

    $check = collect($result->checks)->firstWhere('key', 'title_exists');
    expect($check)->not->toBeNull();
    expect($check->status)->toBe(CheckStatus::Fail);
});

it('passes title length check when within optimal range', function () {
    // 55 characters - within 50-60 range
    $title = str_repeat('a', 55);

    $result = $this->analyzer->analyze([
        'title' => $title,
        'description' => '',
        'focus_keyword' => '',
        'content' => '',
        'slug' => '',
        'url' => '',
    ]);

    $check = collect($result->checks)->firstWhere('key', 'title_length');
    expect($check)->not->toBeNull();
    expect($check->status)->toBe(CheckStatus::Pass);
});

it('warns when title is too short', function () {
    $result = $this->analyzer->analyze([
        'title' => str_repeat('a', 45), // 45 is within the 10 char warning tolerance of min 50
        'description' => '',
        'focus_keyword' => '',
        'content' => '',
        'slug' => '',
        'url' => '',
    ]);

    $check = collect($result->checks)->firstWhere('key', 'title_length');
    expect($check)->not->toBeNull();
    expect($check->status)->toBe(CheckStatus::Warn);
});

it('warns when title is too long', function () {
    $title = str_repeat('a', 65);

    $result = $this->analyzer->analyze([
        'title' => $title,
        'description' => '',
        'focus_keyword' => '',
        'content' => '',
        'slug' => '',
        'url' => '',
    ]);

    $check = collect($result->checks)->firstWhere('key', 'title_length');
    expect($check)->not->toBeNull();
    expect($check->status)->toBe(CheckStatus::Warn);
});

it('passes keyword in title check', function () {
    $result = $this->analyzer->analyze([
        'title' => 'Best Laravel SEO Package for 2025',
        'description' => '',
        'focus_keyword' => 'Laravel SEO',
        'content' => '',
        'slug' => '',
        'url' => '',
    ]);

    $check = collect($result->checks)->firstWhere('key', 'keyword_in_title');
    expect($check)->not->toBeNull();
    expect($check->status)->toBe(CheckStatus::Pass);
});

it('fails keyword in title when keyword not found', function () {
    $result = $this->analyzer->analyze([
        'title' => 'A Completely Unrelated Title',
        'description' => '',
        'focus_keyword' => 'Laravel SEO',
        'content' => '',
        'slug' => '',
        'url' => '',
    ]);

    $check = collect($result->checks)->firstWhere('key', 'keyword_in_title');
    expect($check)->not->toBeNull();
    expect($check->status)->toBe(CheckStatus::Fail);
});

it('passes description exists check', function () {
    $result = $this->analyzer->analyze([
        'title' => '',
        'description' => 'A valid meta description for this page.',
        'focus_keyword' => '',
        'content' => '',
        'slug' => '',
        'url' => '',
    ]);

    $check = collect($result->checks)->firstWhere('key', 'description_exists');
    expect($check)->not->toBeNull();
    expect($check->status)->toBe(CheckStatus::Pass);
});

it('passes description length check when within optimal range', function () {
    $description = str_repeat('a', 140); // Within 120-160

    $result = $this->analyzer->analyze([
        'title' => '',
        'description' => $description,
        'focus_keyword' => '',
        'content' => '',
        'slug' => '',
        'url' => '',
    ]);

    $check = collect($result->checks)->firstWhere('key', 'description_length');
    expect($check)->not->toBeNull();
    expect($check->status)->toBe(CheckStatus::Pass);
});

it('passes keyword in slug check', function () {
    $result = $this->analyzer->analyze([
        'title' => '',
        'description' => '',
        'focus_keyword' => 'laravel seo',
        'content' => '',
        'slug' => 'best-laravel-seo-package',
        'url' => '',
    ]);

    $check = collect($result->checks)->firstWhere('key', 'keyword_in_url');
    expect($check)->not->toBeNull();
    expect($check->status)->toBe(CheckStatus::Pass);
});

it('passes content word count check with sufficient content', function () {
    $words = implode(' ', array_fill(0, 350, 'word'));
    $content = "<p>{$words}</p>";

    $result = $this->analyzer->analyze([
        'title' => '',
        'description' => '',
        'focus_keyword' => '',
        'content' => $content,
        'slug' => '',
        'url' => '',
    ]);

    $check = collect($result->checks)->firstWhere('key', 'content_length');
    expect($check)->not->toBeNull();
    expect($check->status)->toBe(CheckStatus::Pass);
});

it('fails content word count check with insufficient content', function () {
    $result = $this->analyzer->analyze([
        'title' => '',
        'description' => '',
        'focus_keyword' => '',
        'content' => '<p>Very short content.</p>',
        'slug' => '',
        'url' => '',
    ]);

    $check = collect($result->checks)->firstWhere('key', 'content_length');
    expect($check)->not->toBeNull();
    expect($check->status)->toBe(CheckStatus::Fail);
});

it('detects H1 in content', function () {
    $result = $this->analyzer->analyze([
        'title' => '',
        'description' => '',
        'focus_keyword' => '',
        'content' => '<h1>Main Heading</h1><p>Content here</p>',
        'slug' => '',
        'url' => '',
    ]);

    $check = collect($result->checks)->firstWhere('key', 'h1_exists');
    expect($check)->not->toBeNull();
    expect($check->status)->toBe(CheckStatus::Pass);
});

it('detects missing H1 in content', function () {
    $result = $this->analyzer->analyze([
        'title' => '',
        'description' => '',
        'focus_keyword' => '',
        'content' => '<h2>Sub Heading</h2><p>No H1 here</p>',
        'slug' => '',
        'url' => '',
    ]);

    $check = collect($result->checks)->firstWhere('key', 'h1_exists');
    expect($check)->not->toBeNull();
    expect($check->status)->toBe(CheckStatus::Fail);
});

it('returns all 13 checks', function () {
    $result = $this->analyzer->analyze([
        'title' => 'Test Title for SEO Analysis Checking All Items',
        'description' => 'A comprehensive meta description for testing',
        'focus_keyword' => 'seo',
        'content' => '<h1>SEO</h1><h2>Sub</h2><p>Content with <a href="/internal">link</a> and <a href="https://external.com">external</a> and <img alt="test" src="img.jpg" /></p>',
        'slug' => 'seo-test',
        'url' => 'https://example.com/seo-test',
    ]);

    expect($result->checks)->toHaveCount(13);
});
