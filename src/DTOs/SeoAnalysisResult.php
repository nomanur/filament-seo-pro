<?php

declare(strict_types=1);

namespace Nomanur\FilamentSeoPro\DTOs;

use Nomanur\FilamentSeoPro\Enums\CheckStatus;
use Nomanur\FilamentSeoPro\Enums\SeoGrade;

/**
 * Immutable Data Transfer Object representing the complete result of an SEO analysis.
 *
 * Aggregates all individual {@see SeoCheck} results alongside the computed
 * overall score (0-100) and the derived {@see SeoGrade}.
 *
 * @example
 * ```php
 * $result = new SeoAnalysisResult(
 *     checks: [$titleCheck, $descriptionCheck, ...],
 *     score:  78,
 *     grade:  'Good',
 * );
 * ```
 */
final readonly class SeoAnalysisResult
{
    /**
     * @param  SeoCheck[]  $checks  Ordered list of individual SEO check results.
     * @param  int  $score  Overall weighted score (0 – 100).
     * @param  string  $grade  Human-readable grade label (Poor / Fair / Good / Excellent).
     */
    public function __construct(
        public array $checks,
        public int $score,
        public string $grade,
    ) {}

    /**
     * Get only the checks that passed.
     *
     * @return SeoCheck[]
     */
    public function passingChecks(): array
    {
        return array_values(
            array_filter($this->checks, static fn (SeoCheck $check): bool => $check->isPassing()),
        );
    }

    /**
     * Get only the checks that issued a warning.
     *
     * @return SeoCheck[]
     */
    public function warningChecks(): array
    {
        return array_values(
            array_filter(
                $this->checks,
                static fn (SeoCheck $check): bool => $check->status === CheckStatus::Warn,
            ),
        );
    }

    /**
     * Get only the checks that failed.
     *
     * @return SeoCheck[]
     */
    public function failingChecks(): array
    {
        return array_values(
            array_filter($this->checks, static fn (SeoCheck $check): bool => $check->isFailing()),
        );
    }

    /**
     * Get checks grouped by their category.
     *
     * @return array<string, SeoCheck[]> Keyed by category name.
     */
    public function checksByCategory(): array
    {
        $grouped = [];

        foreach ($this->checks as $check) {
            $grouped[$check->category][] = $check;
        }

        return $grouped;
    }

    /**
     * Get the total number of checks.
     */
    public function totalChecks(): int
    {
        return count($this->checks);
    }

    /**
     * Get the number of passing checks.
     */
    public function passCount(): int
    {
        return count($this->passingChecks());
    }

    /**
     * Get the number of failing checks.
     */
    public function failCount(): int
    {
        return count($this->failingChecks());
    }

    /**
     * Resolve the {@see SeoGrade} enum case from the stored score.
     */
    public function gradeEnum(): SeoGrade
    {
        return SeoGrade::fromScore($this->score);
    }

    /**
     * Convert this DTO to an associative array.
     *
     * @return array{
     *     checks: array<int, array{key: string, label: string, status: string, message: string, category: string, weight: int}>,
     *     score: int,
     *     grade: string,
     * }
     */
    public function toArray(): array
    {
        return [
            'checks' => array_map(
                static fn (SeoCheck $check): array => $check->toArray(),
                $this->checks,
            ),
            'score' => $this->score,
            'grade' => $this->grade,
        ];
    }
}
