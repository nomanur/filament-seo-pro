<?php

declare(strict_types=1);

use Nomanur\FilamentSeoPro\Models\SeoMeta;
use Nomanur\FilamentSeoPro\Tests\Models\Post;

it('creates a seo relationship', function () {
    $post = Post::create([
        'title' => 'Test Post',
        'slug' => 'test-post',
        'content' => '<p>Test content</p>',
    ]);

    $seo = $post->seo()->create([
        'title' => 'SEO Title',
        'description' => 'SEO Description',
    ]);

    expect($seo)->toBeInstanceOf(SeoMeta::class);
    expect($post->seo->title)->toBe('SEO Title');
});

it('accesses seo via relationship', function () {
    $post = Post::create([
        'title' => 'Test Post',
        'slug' => 'test-post',
    ]);

    $post->seo()->create([
        'title' => 'My SEO Title',
        'focus_keyword' => 'test',
    ]);

    $post->refresh();

    expect($post->seo)->not->toBeNull();
    expect($post->seo->title)->toBe('My SEO Title');
    expect($post->seo->focus_keyword)->toBe('test');
});

it('getOrCreateSeo creates new meta if none exists', function () {
    $post = Post::create([
        'title' => 'Test Post',
        'slug' => 'test-post',
    ]);

    $seo = $post->getOrCreateSeo();

    expect($seo)->toBeInstanceOf(SeoMeta::class);
    expect($seo->exists)->toBeTrue();
    expect($seo->robots)->toBe('index, follow');
});

it('getOrCreateSeo returns existing meta', function () {
    $post = Post::create([
        'title' => 'Test Post',
        'slug' => 'test-post',
    ]);

    $original = $post->seo()->create([
        'title' => 'Existing Title',
    ]);

    $post->refresh();
    $seo = $post->getOrCreateSeo();

    expect($seo->id)->toBe($original->id);
    expect($seo->title)->toBe('Existing Title');
});

it('cascades delete to seo meta', function () {
    $post = Post::create([
        'title' => 'Test Post',
        'slug' => 'test-post',
    ]);

    $post->seo()->create([
        'title' => 'Will Be Deleted',
    ]);

    $seoId = $post->seo->id;

    $post->delete();

    expect(SeoMeta::find($seoId))->toBeNull();
});

it('updateSeoScore sets the score', function () {
    $post = Post::create([
        'title' => 'Test Post',
        'slug' => 'test-post',
    ]);

    $post->updateSeoScore(85);

    $post->refresh();
    expect($post->seo->seo_score)->toBe(85);
});

it('hasSeoMeta returns false when no meta exists', function () {
    $post = Post::create([
        'title' => 'Test Post',
        'slug' => 'test-post',
    ]);

    expect($post->hasSeoMeta())->toBeFalse();
});

it('hasSeoMeta returns true when meta has title', function () {
    $post = Post::create([
        'title' => 'Test Post',
        'slug' => 'test-post',
    ]);

    $post->seo()->create([
        'title' => 'Has Title',
    ]);

    $post->refresh();
    expect($post->hasSeoMeta())->toBeTrue();
});

it('getSeoAnalysisData returns correct data structure', function () {
    $post = Post::create([
        'title' => 'My Post Title',
        'slug' => 'my-post',
        'content' => '<p>Some content here</p>',
    ]);

    $post->seo()->create([
        'title' => 'SEO Title Override',
        'description' => 'Meta description',
        'focus_keyword' => 'seo keyword',
    ]);

    $post->refresh();
    $data = $post->getSeoAnalysisData();

    expect($data)->toHaveKeys(['title', 'description', 'focus_keyword', 'content', 'slug', 'url']);
    expect($data['title'])->toBe('SEO Title Override'); // SEO title takes priority
    expect($data['description'])->toBe('Meta description');
    expect($data['focus_keyword'])->toBe('seo keyword');
    expect($data['content'])->toBe('<p>Some content here</p>');
    expect($data['slug'])->toBe('my-post');
});

it('getSeoAnalysisData falls back to model title when no seo title', function () {
    $post = Post::create([
        'title' => 'Model Title',
        'slug' => 'model-slug',
        'content' => '<p>Content</p>',
    ]);

    $data = $post->getSeoAnalysisData();

    expect($data['title'])->toBe('Model Title');
    expect($data['slug'])->toBe('model-slug');
});
