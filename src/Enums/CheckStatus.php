<?php

declare(strict_types=1);

namespace Nomanur\FilamentSeoPro\Enums;

/**
 * Represents the status outcome of an individual SEO check.
 *
 * Each case maps to a semantic severity level:
 * - **Pass**: The check criterion is fully satisfied.
 * - **Warn**: The check criterion is partially met or could be improved.
 * - **Fail**: The check criterion is not met and requires attention.
 */
enum CheckStatus: string
{
    case Pass = 'pass';
    case Warn = 'warn';
    case Fail = 'fail';

    /**
     * Get the human-readable label for this status.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pass => 'Passed',
            self::Warn => 'Warning',
            self::Fail => 'Failed',
        };
    }

    /**
     * Get the Filament-compatible color identifier for this status.
     */
    public function color(): string
    {
        return match ($this) {
            self::Pass => 'success',
            self::Warn => 'warning',
            self::Fail => 'danger',
        };
    }

    /**
     * Get the Heroicon name associated with this status.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Pass => 'heroicon-o-check-circle',
            self::Warn => 'heroicon-o-exclamation-triangle',
            self::Fail => 'heroicon-o-x-circle',
        };
    }

    /**
     * Determine whether this status represents a passing check.
     */
    public function isPassing(): bool
    {
        return $this === self::Pass;
    }

    /**
     * Determine whether this status represents a failing check.
     */
    public function isFailing(): bool
    {
        return $this === self::Fail;
    }

    /**
     * Get the numeric multiplier used when calculating weighted scores.
     *
     * - Pass  → 1.0 (full weight)
     * - Warn  → 0.5 (half weight)
     * - Fail  → 0.0 (no weight)
     */
    public function scoreMultiplier(): float
    {
        return match ($this) {
            self::Pass => 1.0,
            self::Warn => 0.5,
            self::Fail => 0.0,
        };
    }
}
