<?php

declare(strict_types=1);

namespace Nomanur\FilamentSeoPro\DTOs;

/**
 * Immutable Data Transfer Object representing the result of a readability analysis.
 *
 * Contains a numeric score (0 – 100), a human-readable grade, and granular
 * details about sentence length, paragraph structure, passive voice usage,
 * and transition word frequency.
 *
 * @example
 * ```php
 * $result = new ReadabilityResult(
 *     score:   72,
 *     grade:   'Good',
 *     details: [
 *         'avgSentenceLength'       => 16.4,
 *         'paragraphCount'          => 5,
 *         'passiveVoicePercentage'  => 8.3,
 *         'transitionWordCount'     => 7,
 *     ],
 * );
 * ```
 */
final readonly class ReadabilityResult
{
    /**
     * @param  int  $score  Overall readability score (0 – 100).
     * @param  string  $grade  Human-readable grade label (Poor / Fair / Good / Excellent).
     * @param  array{
     *     avgSentenceLength: float,
     *     paragraphCount: int,
     *     passiveVoicePercentage: float,
     *     transitionWordCount: int,
     * }  $details  Breakdown of the individual readability metrics.
     */
    public function __construct(
        public int $score,
        public string $grade,
        public array $details,
    ) {}

    /**
     * Get the average number of words per sentence.
     */
    public function avgSentenceLength(): float
    {
        return (float) ($this->details['avgSentenceLength']);
    }

    /**
     * Get the total number of paragraphs detected.
     */
    public function paragraphCount(): int
    {
        return (int) ($this->details['paragraphCount']);
    }

    /**
     * Get the percentage of sentences detected as passive voice.
     */
    public function passiveVoicePercentage(): float
    {
        return (float) ($this->details['passiveVoicePercentage']);
    }

    /**
     * Get the total count of transition words / phrases found.
     */
    public function transitionWordCount(): int
    {
        return (int) ($this->details['transitionWordCount']);
    }

    /**
     * Determine whether the readability score is considered acceptable (≥ 61).
     */
    public function isAcceptable(): bool
    {
        return $this->score >= 61;
    }

    /**
     * Convert this DTO to an associative array.
     *
     * @return array{
     *     score: int,
     *     grade: string,
     *     details: array{
     *         avgSentenceLength: float,
     *         paragraphCount: int,
     *         passiveVoicePercentage: float,
     *         transitionWordCount: int,
     *     },
     * }
     */
    public function toArray(): array
    {
        return [
            'score' => $this->score,
            'grade' => $this->grade,
            'details' => $this->details,
        ];
    }
}
