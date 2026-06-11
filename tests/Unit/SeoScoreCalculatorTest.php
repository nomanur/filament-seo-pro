<?php

declare(strict_types=1);

use Nomanur\FilamentSeoPro\DTOs\SeoAnalysisResult;
use Nomanur\FilamentSeoPro\DTOs\SeoCheck;
use Nomanur\FilamentSeoPro\Enums\CheckStatus;
use Nomanur\FilamentSeoPro\Services\SeoScoreCalculator;

beforeEach(function () {
    $this->calculator = app(SeoScoreCalculator::class);
});

it('returns score and grade', function () {
    $result = new SeoAnalysisResult(
        checks: [
            new SeoCheck('test', 'Test', CheckStatus::Pass, 'Good', 'Test', 10),
        ],
        score: 0,
        grade: '',
    );

    $scoreData = $this->calculator->calculate($result);

    expect($scoreData)->toHaveKeys(['score', 'grade']);
    expect($scoreData['score'])->toBeInt();
    expect($scoreData['grade'])->toBeString();
});

it('calculates 100 for all passing checks', function () {
    $checks = [];
    for ($i = 0; $i < 10; $i++) {
        $checks[] = new SeoCheck("check_{$i}", "Check {$i}", CheckStatus::Pass, 'Good', 'Test', 10);
    }

    $result = new SeoAnalysisResult(checks: $checks, score: 0, grade: '');
    $scoreData = $this->calculator->calculate($result);

    expect($scoreData['score'])->toBe(100);
    expect($scoreData['grade'])->toBe('Excellent');
});

it('calculates 0 for all failing checks', function () {
    $checks = [];
    for ($i = 0; $i < 10; $i++) {
        $checks[] = new SeoCheck("check_{$i}", "Check {$i}", CheckStatus::Fail, 'Bad', 'Test', 10);
    }

    $result = new SeoAnalysisResult(checks: $checks, score: 0, grade: '');
    $scoreData = $this->calculator->calculate($result);

    expect($scoreData['score'])->toBe(0);
    expect($scoreData['grade'])->toBe('Poor');
});

it('gives partial credit for warnings', function () {
    $checks = [
        new SeoCheck('a', 'A', CheckStatus::Pass, 'Good', 'Test', 50),
        new SeoCheck('b', 'B', CheckStatus::Warn, 'Warn', 'Test', 50),
    ];

    $result = new SeoAnalysisResult(checks: $checks, score: 0, grade: '');
    $scoreData = $this->calculator->calculate($result);

    // Should be between 50 and 100 since warnings get partial credit
    expect($scoreData['score'])->toBeGreaterThan(50);
    expect($scoreData['score'])->toBeLessThan(100);
});

it('returns correct grade for poor score', function () {
    $checks = [
        new SeoCheck('a', 'A', CheckStatus::Pass, 'Good', 'Test', 20),
        new SeoCheck('b', 'B', CheckStatus::Fail, 'Bad', 'Test', 80),
    ];

    $result = new SeoAnalysisResult(checks: $checks, score: 0, grade: '');
    $scoreData = $this->calculator->calculate($result);

    expect($scoreData['score'])->toBeLessThanOrEqual(30);
    expect($scoreData['grade'])->toBe('Poor');
});

it('returns correct grade for fair score', function () {
    $checks = [
        new SeoCheck('a', 'A', CheckStatus::Pass, 'Good', 'Test', 50),
        new SeoCheck('b', 'B', CheckStatus::Fail, 'Bad', 'Test', 50),
    ];

    $result = new SeoAnalysisResult(checks: $checks, score: 0, grade: '');
    $scoreData = $this->calculator->calculate($result);

    expect($scoreData['score'])->toBe(50);
    expect($scoreData['grade'])->toBe('Fair');
});

it('returns correct grade for good score', function () {
    $checks = [
        new SeoCheck('a', 'A', CheckStatus::Pass, 'Good', 'Test', 75),
        new SeoCheck('b', 'B', CheckStatus::Fail, 'Bad', 'Test', 25),
    ];

    $result = new SeoAnalysisResult(checks: $checks, score: 0, grade: '');
    $scoreData = $this->calculator->calculate($result);

    expect($scoreData['score'])->toBe(75);
    expect($scoreData['grade'])->toBe('Good');
});

it('handles empty checks array', function () {
    $result = new SeoAnalysisResult(checks: [], score: 0, grade: '');
    $scoreData = $this->calculator->calculate($result);

    expect($scoreData['score'])->toBe(0);
    expect($scoreData['grade'])->toBe('Poor');
});
