<?php

declare(strict_types=1);

namespace Nomanur\FilamentSeoPro\DTOs;

use Nomanur\FilamentSeoPro\Enums\CheckStatus;

/**
 * Immutable Data Transfer Object representing a single SEO check result.
 *
 * Each instance captures the outcome of one specific SEO rule evaluation,
 * including its status (pass / warn / fail), a human-readable message, the
 * category it belongs to, and its relative weight in the overall score.
 *
 * @example
 * ```php
 * $check = new SeoCheck(
 *     key:      'title_length',
 *     label:    'Title Length',
 *     status:   CheckStatus::Pass,
 *     message:  'Title is 55 characters — within the recommended 50-60 range.',
 *     category: 'Title',
 *     weight:   10,
 * );
 * ```
 */
final readonly class SeoCheck
{
    /**
     * @param  string  $key  Unique machine-readable identifier (e.g. 'title_length').
     * @param  string  $label  Human-readable name shown in the UI (e.g. 'Title Length').
     * @param  CheckStatus  $status  The outcome status of this check.
     * @param  string  $message  Explanation of why the check passed, warned, or failed.
     * @param  string  $category  Logical grouping (e.g. 'Title', 'Content', 'Links').
     * @param  int  $weight  Relative importance of this check (higher = more impact).
     */
    public function __construct(
        public string $key,
        public string $label,
        public CheckStatus $status,
        public string $message,
        public string $category,
        public int $weight,
    ) {}

    /**
     * Create a new SeoCheck instance from an associative array.
     *
     * @param  array{
     *     key: string,
     *     label: string,
     *     status: CheckStatus|string,
     *     message: string,
     *     category: string,
     *     weight: int,
     * }  $data
     */
    public static function fromArray(array $data): self
    {
        $status = $data['status'] instanceof CheckStatus
            ? $data['status']
            : CheckStatus::from($data['status']);

        return new self(
            key: $data['key'],
            label: $data['label'],
            status: $status,
            message: $data['message'],
            category: $data['category'],
            weight: $data['weight'],
        );
    }

    /**
     * Convert this DTO to an associative array.
     *
     * @return array{
     *     key: string,
     *     label: string,
     *     status: string,
     *     message: string,
     *     category: string,
     *     weight: int,
     * }
     */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'status' => $this->status->value,
            'message' => $this->message,
            'category' => $this->category,
            'weight' => $this->weight,
        ];
    }

    /**
     * Determine whether this check passed.
     */
    public function isPassing(): bool
    {
        return $this->status->isPassing();
    }

    /**
     * Determine whether this check failed.
     */
    public function isFailing(): bool
    {
        return $this->status->isFailing();
    }

    /**
     * Get the weighted score contribution of this check.
     *
     * A passing check contributes its full weight, a warning contributes
     * half, and a failure contributes nothing.
     */
    public function weightedScore(): float
    {
        return $this->weight * $this->status->scoreMultiplier();
    }
}
