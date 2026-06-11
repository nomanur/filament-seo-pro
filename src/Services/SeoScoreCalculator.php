<?php

declare(strict_types=1);

namespace Nomanur\FilamentSeoPro\Services;

use Nomanur\FilamentSeoPro\DTOs\SeoAnalysisResult;
use Nomanur\FilamentSeoPro\DTOs\SeoCheck;
use Nomanur\FilamentSeoPro\Enums\SeoGrade;

/**
 * Calculates the overall SEO score and grade from an analysis result.
 *
 * Uses a **weighted scoring** algorithm:
 *
 * ```
 * score = (sum of each check's weightedScore) / (sum of all weights) × 100
 * ```
 *
 * Where each check's `weightedScore` is determined by its status:
 * - Pass → full weight  (weight × 1.0)
 * - Warn → half weight  (weight × 0.5)
 * - Fail → zero         (weight × 0.0)
 *
 * The resulting score (0 – 100) is then mapped to a {@see SeoGrade}.
 */
class SeoScoreCalculator
{
    /**
     * Calculate the weighted score and grade from the given analysis result.
     *
     * @param  SeoAnalysisResult  $result  The analysis result whose checks will be scored.
     * @return array{score: int, grade: string} The overall score (0-100) and human-readable grade.
     */
    public function calculate(SeoAnalysisResult $result): array
    {
        $checks = $result->checks;

        if ($checks === []) {
            return [
                'score' => 0,
                'grade' => SeoGrade::Poor->label(),
            ];
        }

        $totalWeight = $this->totalWeight($checks);
        $earnedWeight = $this->earnedWeight($checks);

        // Prevent division by zero (should never happen with valid checks)
        $score = $totalWeight > 0
            ? (int) round(($earnedWeight / $totalWeight) * 100)
            : 0;

        // Clamp to 0-100 for safety
        $score = max(0, min(100, $score));

        $grade = SeoGrade::fromScore($score);

        return [
            'score' => $score,
            'grade' => $grade->label(),
        ];
    }

    /**
     * Sum up the maximum possible weight across all checks.
     *
     * @param  SeoCheck[]  $checks
     */
    protected function totalWeight(array $checks): float
    {
        return array_reduce(
            $checks,
            static fn (float $carry, SeoCheck $check): float => $carry + $check->weight,
            0.0,
        );
    }

    /**
     * Sum up the actually earned (status-adjusted) weight across all checks.
     *
     * @param  SeoCheck[]  $checks
     */
    protected function earnedWeight(array $checks): float
    {
        return array_reduce(
            $checks,
            static fn (float $carry, SeoCheck $check): float => $carry + $check->weightedScore(),
            0.0,
        );
    }
}
