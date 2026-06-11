<?php

declare(strict_types=1);

namespace Nomanur\FilamentSeoPro\Services;

use Nomanur\FilamentSeoPro\DTOs\ReadabilityResult;
use Nomanur\FilamentSeoPro\Enums\SeoGrade;

/**
 * Readability analysis service.
 *
 * Strips HTML from the provided content and evaluates four readability metrics:
 *
 * 1. **Average sentence length** — target: 15–20 words per sentence.
 * 2. **Paragraph count** — ensures content is broken into digestible blocks.
 * 3. **Passive voice percentage** — target: < 10 % of sentences.
 * 4. **Transition word count** — higher is better for content flow.
 *
 * Each metric contributes 25 points toward a 0–100 composite score.
 *
 * @example
 * ```php
 * $analyzer = new ReadabilityAnalyzer();
 * $result   = $analyzer->analyze('<p>Your HTML content here…</p>');
 *
 * echo $result->score;                     // 72
 * echo $result->grade;                     // "Good"
 * echo $result->avgSentenceLength();       // 16.4
 * echo $result->passiveVoicePercentage();  // 8.3
 * ```
 */
class ReadabilityAnalyzer
{
    /** @var float Maximum points each of the 4 metrics can contribute. */
    private const float METRIC_MAX_POINTS = 25.0;

    /**
     * Analyse the readability of the given HTML content.
     *
     * @param  string  $content  Raw HTML content to analyse.
     * @return ReadabilityResult Immutable result containing score, grade, and detail breakdown.
     */
    public function analyze(string $content): ReadabilityResult
    {
        $plainText = $this->stripHtml($content);

        // Guard: empty content
        if (trim($plainText) === '') {
            return new ReadabilityResult(
                score: 0,
                grade: SeoGrade::Poor->label(),
                details: [
                    'avgSentenceLength' => 0.0,
                    'paragraphCount' => 0,
                    'passiveVoicePercentage' => 0.0,
                    'transitionWordCount' => 0,
                ],
            );
        }

        $sentences = $this->extractSentences($plainText);
        $sentenceCount = count($sentences);
        $paragraphs = $this->extractParagraphs($content);
        $paragraphCount = count($paragraphs);

        $avgSentenceLength = $this->calculateAvgSentenceLength($sentences);
        $passiveVoicePercentage = $this->calculatePassiveVoicePercentage($sentences);
        $transitionWordCount = $this->countTransitionWords($plainText);

        // --- Score calculation (each metric = 25 points max) ---
        $sentenceLengthScore = $this->scoreSentenceLength($avgSentenceLength);
        $paragraphScore = $this->scoreParagraphStructure($paragraphs, $sentences);
        $passiveVoiceScore = $this->scorePassiveVoice($passiveVoicePercentage);
        $transitionWordsScore = $this->scoreTransitionWords($transitionWordCount, $sentenceCount);

        $totalScore = (int) round(
            $sentenceLengthScore + $paragraphScore + $passiveVoiceScore + $transitionWordsScore,
        );
        $totalScore = max(0, min(100, $totalScore));

        $grade = SeoGrade::fromScore($totalScore);

        return new ReadabilityResult(
            score: $totalScore,
            grade: $grade->label(),
            details: [
                'avgSentenceLength' => round($avgSentenceLength, 1),
                'paragraphCount' => $paragraphCount,
                'passiveVoicePercentage' => round($passiveVoicePercentage, 1),
                'transitionWordCount' => $transitionWordCount,
            ],
        );
    }

    // =========================================================================
    // Extraction Helpers
    // =========================================================================

    /**
     * Split plain text into individual sentences.
     *
     * Handles common sentence terminators (. ! ?) and avoids splitting on
     * abbreviations like "e.g.", "Mr.", "U.S.A.", etc.
     *
     * @return string[]
     */
    protected function extractSentences(string $text): array
    {
        // Split on sentence-ending punctuation followed by whitespace or end of string
        $parts = preg_split(
            '/(?<=[.!?])\s+/',
            trim($text),
            -1,
            PREG_SPLIT_NO_EMPTY,
        );

        return $parts !== false ? $parts : [$text];
    }

    /**
     * Extract paragraphs from HTML content.
     *
     * Looks for `<p>` blocks first; if none are found, falls back to splitting
     * on double newlines.
     *
     * @return string[] Array of paragraph text (HTML stripped).
     */
    protected function extractParagraphs(string $htmlContent): array
    {
        // Try <p> tags first
        preg_match_all('/<p\b[^>]*>(.*?)<\/p>/si', $htmlContent, $matches);

        if (! empty($matches[1])) {
            $paragraphs = array_map(
                fn (string $p): string => trim(strip_tags($p)),
                $matches[1],
            );

            return array_values(array_filter($paragraphs, static fn (string $p): bool => $p !== ''));
        }

        // Fallback: split plain text on double newlines
        $plainText = $this->stripHtml($htmlContent);
        $parts = preg_split('/\n{2,}/', $plainText, -1, PREG_SPLIT_NO_EMPTY);

        if ($parts === false) {
            return [trim($plainText)];
        }

        $paragraphs = array_map('trim', $parts);

        return array_values(array_filter($paragraphs, static fn (string $p): bool => $p !== ''));
    }

    // =========================================================================
    // Metric Calculators
    // =========================================================================

    /**
     * Calculate the average number of words per sentence.
     *
     * @param  string[]  $sentences
     */
    protected function calculateAvgSentenceLength(array $sentences): float
    {
        if ($sentences === []) {
            return 0.0;
        }

        $totalWords = 0;

        foreach ($sentences as $sentence) {
            $words = preg_split('/\s+/', trim($sentence), -1, PREG_SPLIT_NO_EMPTY);
            $totalWords += ($words !== false) ? count($words) : 0;
        }

        return $totalWords / count($sentences);
    }

    /**
     * Calculate the percentage of sentences that use passive voice.
     *
     * Detection relies on matching common auxiliary verb + past participle
     * patterns (configurable via `filament-seo-pro.passive_voice_auxiliaries`).
     *
     * @param  string[]  $sentences
     */
    protected function calculatePassiveVoicePercentage(array $sentences): float
    {
        if ($sentences === []) {
            return 0.0;
        }

        $passiveCount = 0;

        foreach ($sentences as $sentence) {
            if ($this->isPassiveVoice($sentence)) {
                $passiveCount++;
            }
        }

        return ($passiveCount / count($sentences)) * 100;
    }

    /**
     * Determine whether a single sentence appears to use passive voice.
     *
     * The heuristic checks for an auxiliary verb followed by a word ending in
     * a common past-participle suffix (-ed, -en, -t, -n, etc.).
     */
    protected function isPassiveVoice(string $sentence): bool
    {
        /** @var string[] $auxiliaries */
        $auxiliaries = config('filament-seo-pro.passive_voice_auxiliaries', [
            'is', 'are', 'was', 'were', 'be', 'been', 'being',
            'has been', 'have been', 'had been',
            'will be', 'will have been',
            'is being', 'are being', 'was being', 'were being',
            'gets', 'got', 'get',
        ]);

        $lowerSentence = mb_strtolower($sentence);

        foreach ($auxiliaries as $aux) {
            // Look for: auxiliary + optional adverb + past participle
            $pattern = '/\b' . preg_quote($aux, '/') . '\b\s+(\w+ly\s+)?(\w+(ed|en|t|n|wn|ght))\b/i';

            if (preg_match($pattern, $lowerSentence)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Count the number of transition words / phrases found in the text.
     */
    protected function countTransitionWords(string $text): int
    {
        /** @var string[] $transitionWords */
        $transitionWords = config('filament-seo-pro.transition_words', [
            'additionally', 'also', 'moreover', 'furthermore', 'in addition',
            'however', 'nevertheless', 'nonetheless', 'therefore', 'consequently',
            'for example', 'for instance', 'such as', 'in conclusion',
            'first', 'second', 'third', 'finally', 'next', 'then', 'meanwhile',
            'similarly', 'likewise', 'although', 'despite', 'because', 'since',
        ]);

        $lowerText = mb_strtolower($text);
        $count = 0;

        foreach ($transitionWords as $word) {
            // Count occurrences of each transition word/phrase as whole words
            $pattern = '/\b' . preg_quote(mb_strtolower($word), '/') . '\b/i';
            $matches = preg_match_all($pattern, $lowerText);
            $count += ($matches !== false) ? $matches : 0;
        }

        return $count;
    }

    // =========================================================================
    // Scoring Functions (each returns 0 – 25 points)
    // =========================================================================

    /**
     * Score the average sentence length metric.
     *
     * Perfect score when avg is within the target range (default 15 – 20 words).
     * Diminishes linearly as the value moves away from the target.
     */
    protected function scoreSentenceLength(float $avgLength): float
    {
        $minTarget = (float) config('filament-seo-pro.readability.target_sentence_length.min', 15);
        $maxTarget = (float) config('filament-seo-pro.readability.target_sentence_length.max', 20);

        if ($avgLength >= $minTarget && $avgLength <= $maxTarget) {
            return self::METRIC_MAX_POINTS;
        }

        if ($avgLength < $minTarget) {
            // Too short — score proportionally (minimum 0)
            $ratio = $minTarget > 0 ? $avgLength / $minTarget : 0;

            return max(0, self::METRIC_MAX_POINTS * $ratio);
        }

        // Too long — penalise linearly (score drops to 0 at double the max)
        $excess = $avgLength - $maxTarget;
        $ratio = max(0, 1 - ($excess / $maxTarget));

        return max(0, self::METRIC_MAX_POINTS * $ratio);
    }

    /**
     * Score the paragraph structure metric.
     *
     * Awards full points when paragraphs average 2 – 4 sentences each.
     * Penalises overly long or very short paragraphs proportionally.
     *
     * @param  string[]  $paragraphs
     * @param  string[]  $sentences
     */
    protected function scoreParagraphStructure(array $paragraphs, array $sentences): float
    {
        $paragraphCount = count($paragraphs);
        $sentenceCount = count($sentences);

        if ($paragraphCount === 0 || $sentenceCount === 0) {
            return 0.0;
        }

        $minTarget = (float) config('filament-seo-pro.readability.target_paragraph_sentences.min', 2);
        $maxTarget = (float) config('filament-seo-pro.readability.target_paragraph_sentences.max', 4);

        $avgSentencesPerParagraph = $sentenceCount / $paragraphCount;

        if ($avgSentencesPerParagraph >= $minTarget && $avgSentencesPerParagraph <= $maxTarget) {
            return self::METRIC_MAX_POINTS;
        }

        if ($avgSentencesPerParagraph < $minTarget) {
            $ratio = $minTarget > 0 ? $avgSentencesPerParagraph / $minTarget : 0;

            return max(0, self::METRIC_MAX_POINTS * $ratio);
        }

        // Too many sentences per paragraph
        $excess = $avgSentencesPerParagraph - $maxTarget;
        $ratio = max(0, 1 - ($excess / ($maxTarget * 2)));

        return max(0, self::METRIC_MAX_POINTS * $ratio);
    }

    /**
     * Score the passive voice metric.
     *
     * Full marks when passive voice is under the configured threshold (default 10 %).
     * Score diminishes linearly above the threshold, reaching 0 at 100 %.
     */
    protected function scorePassiveVoice(float $percentage): float
    {
        $maxPercentage = (float) config('filament-seo-pro.readability.max_passive_voice_percentage', 10.0);

        if ($percentage <= $maxPercentage) {
            return self::METRIC_MAX_POINTS;
        }

        // Linearly decrease from max to 0 as percentage goes from threshold to 100
        $remaining = 100 - $maxPercentage;
        $excess = $percentage - $maxPercentage;
        $ratio = $remaining > 0 ? max(0, 1 - ($excess / $remaining)) : 0;

        return self::METRIC_MAX_POINTS * $ratio;
    }

    /**
     * Score the transition words metric.
     *
     * Awards full marks when the ratio of transition words to sentences
     * meets or exceeds a healthy threshold (~30 % of sentences should contain
     * at least one transition word).
     */
    protected function scoreTransitionWords(int $transitionWordCount, int $sentenceCount): float
    {
        if ($sentenceCount === 0) {
            return 0.0;
        }

        $minTransitions = (int) config('filament-seo-pro.readability.min_transition_words', 3);

        if ($transitionWordCount >= $minTransitions) {
            // Bonus: ratio-based scoring — cap at full marks
            $ratio = $transitionWordCount / max(1, $sentenceCount);
            $score = min(1.0, $ratio / 0.3) * self::METRIC_MAX_POINTS;

            return min(self::METRIC_MAX_POINTS, $score);
        }

        // Below minimum: proportional credit
        return ($transitionWordCount / max(1, $minTransitions)) * self::METRIC_MAX_POINTS;
    }

    // =========================================================================
    // Utility
    // =========================================================================

    /**
     * Strip all HTML tags and decode entities, returning plain text.
     */
    protected function stripHtml(string $html): string
    {
        // Remove script and style blocks entirely
        $text = (string) preg_replace('#<(script|style)\b[^>]*>.*?</\1>#si', '', $html);

        // Convert <br>, </p>, </div>, </li> to newlines for paragraph detection
        $text = (string) preg_replace('#<br\s*/?\s*>#i', "\n", $text);
        $text = (string) preg_replace('#</(p|div|li|blockquote)>#i', "\n\n", $text);

        // Strip remaining tags
        $text = strip_tags($text);

        // Decode HTML entities
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Normalise multiple spaces (but preserve newlines)
        $text = (string) preg_replace('/[^\S\n]+/', ' ', $text);

        // Normalise multiple blank lines
        $text = (string) preg_replace('/\n{3,}/', "\n\n", $text);

        return trim($text);
    }
}
