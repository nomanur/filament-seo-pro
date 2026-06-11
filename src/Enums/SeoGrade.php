<?php

declare(strict_types=1);

namespace Nomanur\FilamentSeoPro\Enums;

/**
 * Represents the overall SEO grade derived from a numeric score.
 *
 * Grade thresholds (configurable via `filament-seo-pro.grade_thresholds`):
 *  - **Excellent**: 81 – 100
 *  - **Good**:      61 – 80
 *  - **Fair**:      31 – 60
 *  - **Poor**:       0 – 30
 */
enum SeoGrade: string
{
    case Poor = 'poor';
    case Fair = 'fair';
    case Good = 'good';
    case Excellent = 'excellent';

    /**
     * Resolve the appropriate grade for the given numeric score.
     *
     * @param  int  $score  A value between 0 and 100 (inclusive).
     *
     * @throws \InvalidArgumentException If the score is outside the 0-100 range.
     */
    public static function fromScore(int $score): self
    {
        if ($score < 0 || $score > 100) {
            throw new \InvalidArgumentException(
                "SEO score must be between 0 and 100, [{$score}] given."
            );
        }

        $thresholds = config('filament-seo-pro.grade_thresholds', [
            'excellent' => 81,
            'good' => 61,
            'fair' => 31,
        ]);

        return match (true) {
            $score >= $thresholds['excellent'] => self::Excellent,
            $score >= $thresholds['good'] => self::Good,
            $score >= $thresholds['fair'] => self::Fair,
            default => self::Poor,
        };
    }

    /**
     * Get the human-readable label for this grade.
     */
    public function label(): string
    {
        return match ($this) {
            self::Poor => 'Poor',
            self::Fair => 'Fair',
            self::Good => 'Good',
            self::Excellent => 'Excellent',
        };
    }

    /**
     * Get the Filament-compatible color identifier for this grade.
     */
    public function color(): string
    {
        return match ($this) {
            self::Poor => 'danger',
            self::Fair => 'warning',
            self::Good => 'info',
            self::Excellent => 'success',
        };
    }

    /**
     * Get the Heroicon name associated with this grade.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Poor => 'heroicon-o-x-circle',
            self::Fair => 'heroicon-o-exclamation-triangle',
            self::Good => 'heroicon-o-check-circle',
            self::Excellent => 'heroicon-o-star',
        };
    }

    /**
     * Get the emoji representation for quick visual reference.
     */
    public function emoji(): string
    {
        return match ($this) {
            self::Poor => '🔴',
            self::Fair => '🟡',
            self::Good => '🟢',
            self::Excellent => '⭐',
        };
    }
}
