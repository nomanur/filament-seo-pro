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
            'title' => $get('seo.title') ?? $get('title') ?? '',
            'description' => $get('seo.description') ?? '',
            'focus_keyword' => $get('seo.focus_keyword') ?? '',
            'content' => $get('content') ?? '',
            'slug' => $get('slug') ?? '',
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
}
