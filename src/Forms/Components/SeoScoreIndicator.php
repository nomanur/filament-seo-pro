<?php

declare(strict_types=1);

namespace Nomanur\FilamentSeoPro\Forms\Components;

use Filament\Schemas\Components\Component;
use Nomanur\FilamentSeoPro\Services\SeoAnalyzer;
use Nomanur\FilamentSeoPro\Services\SeoScoreCalculator;

/**
 * SEO Score indicator component.
 *
 * Displays a circular gauge showing the SEO score (0-100) with
 * color-coded grades: Poor (red), Fair (orange), Good (blue), Excellent (green).
 */
class SeoScoreIndicator extends Component
{
    protected string $view = 'filament-seo-pro::components.seo-score';

    public static function make(string $name = 'seo_score_indicator'): static
    {
        $static = app(static::class);

        $static->statePath($name);

        return $static;
    }

    /**
     * Calculate the SEO score from the current form state.
     *
     * @return array{score: int, grade: string, color: string}
     */
    public function calculateScore(callable $get): array
    {
        $analyzer = app(SeoAnalyzer::class);
        $calculator = app(SeoScoreCalculator::class);

        $data = [
            'title' => $this->normalizeState($get('seo.title') ?? $get('title') ?? ''),
            'description' => $this->normalizeState($get('seo.description') ?? ''),
            'focus_keyword' => $this->normalizeState($get('seo.focus_keyword') ?? ''),
            'content' => $this->normalizeState($get('content') ?? ''),
            'slug' => $this->normalizeState($get('slug') ?? ''),
            'url' => '',
        ];

        $result = $analyzer->analyze($data);
        $scoreData = $calculator->calculate($result);

        return [
            'score' => $scoreData['score'],
            'grade' => $scoreData['grade'],
            'color' => match (true) {
                $scoreData['score'] <= 30 => 'danger',
                $scoreData['score'] <= 60 => 'warning',
                $scoreData['score'] <= 80 => 'info',
                default => 'success',
            },
        ];
    }

    /**
     * Normalize a state value (e.g. string, translatable array, or object) to string.
     */
    protected function normalizeState(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_array($value)) {
            if (empty($value)) {
                return '';
            }

            $locale = app()->getLocale();
            if (isset($value[$locale])) {
                return $this->normalizeState($value[$locale]);
            }

            return $this->normalizeState(reset($value));
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        }

        return '';
    }
}
