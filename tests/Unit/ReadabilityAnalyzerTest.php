<?php

declare(strict_types=1);

use Nomanur\FilamentSeoPro\DTOs\ReadabilityResult;
use Nomanur\FilamentSeoPro\Services\ReadabilityAnalyzer;

beforeEach(function () {
    $this->analyzer = app(ReadabilityAnalyzer::class);
});

it('returns a ReadabilityResult', function () {
    $result = $this->analyzer->analyze('<p>This is a simple test paragraph with some words in it.</p>');

    expect($result)->toBeInstanceOf(ReadabilityResult::class);
    expect($result->score)->toBeInt();
    expect($result->score)->toBeGreaterThanOrEqual(0);
    expect($result->score)->toBeLessThanOrEqual(100);
});

it('returns a grade', function () {
    $result = $this->analyzer->analyze('<p>Some content.</p>');

    expect($result->grade)->toBeString();
    expect($result->grade)->toBeIn(['Poor', 'Fair', 'Good', 'Excellent']);
});

it('includes detail metrics', function () {
    $result = $this->analyzer->analyze('<p>This is a sentence. This is another sentence.</p>');

    expect($result->details)->toHaveKeys([
        'avgSentenceLength',
        'paragraphCount',
        'passiveVoicePercentage',
        'transitionWordCount',
    ]);
});

it('scores well-written content highly', function () {
    $content = '<p>Writing good content is important for SEO. However, many people overlook readability. Therefore, you should focus on clear sentences.</p>
    <p>Additionally, using transition words helps your reader follow along. Furthermore, short paragraphs make content easier to scan.</p>
    <p>In conclusion, readable content ranks better in search engines. Moreover, your visitors will stay longer on your page.</p>';

    $result = $this->analyzer->analyze($content);

    expect($result->score)->toBeGreaterThanOrEqual(50);
});

it('scores poorly written content lower', function () {
    // Very long run-on sentence
    $content = '<p>' . implode(' ', array_fill(0, 100, 'word')) . '.</p>';

    $result = $this->analyzer->analyze($content);

    expect($result->score)->toBeLessThan(80);
});

it('handles empty content', function () {
    $result = $this->analyzer->analyze('');

    expect($result->score)->toBe(0);
    expect($result->grade)->toBe('Poor');
});

it('strips HTML tags before analysis', function () {
    $result = $this->analyzer->analyze('<div class="test"><p><strong>Bold</strong> and <em>italic</em> text here.</p></div>');

    expect($result->details['paragraphCount'])->toBeGreaterThanOrEqual(1);
});

it('detects multiple paragraphs', function () {
    $content = '<p>First paragraph with some text.</p><p>Second paragraph with more text.</p><p>Third paragraph.</p>';

    $result = $this->analyzer->analyze($content);

    expect($result->details['paragraphCount'])->toBe(3);
});
