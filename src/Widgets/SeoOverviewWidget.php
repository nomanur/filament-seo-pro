<?php

declare(strict_types=1);

namespace Nomanur\FilamentSeoPro\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;
use Nomanur\FilamentSeoPro\Models\SeoMeta;

/**
 * Dashboard widget showing SEO overview statistics.
 *
 * Displays:
 * - Average SEO Score across all content
 * - Count of missing SEO titles
 * - Count of missing meta descriptions
 * - List of lowest-scoring content
 */
class SeoOverviewWidget extends Widget
{
    protected string $view = 'filament-seo-pro::widgets.seo-overview';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 10;

    /**
     * Get the average SEO score across all tracked content.
     */
    public function getAverageScore(): int
    {
        $avg = SeoMeta::query()->avg('seo_score');

        return (int) round($avg ?? 0);
    }

    /**
     * Get count of records missing SEO titles.
     */
    public function getMissingTitlesCount(): int
    {
        return SeoMeta::query()
            ->whereNull('title')
            ->orWhere('title', '')
            ->count();
    }

    /**
     * Get count of records missing meta descriptions.
     */
    public function getMissingDescriptionsCount(): int
    {
        return SeoMeta::query()
            ->whereNull('description')
            ->orWhere('description', '')
            ->count();
    }

    /**
     * Get the lowest-scoring content items.
     *
     * @return Collection<int, SeoMeta>
     */
    public function getLowestScoring(int $limit = 5): Collection
    {
        return SeoMeta::query()
            ->where('seo_score', '>', 0)
            ->orderBy('seo_score', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get total tracked content count.
     */
    public function getTotalContentCount(): int
    {
        return SeoMeta::query()->count();
    }

    /**
     * Get the percentage of content with good+ scores (>60).
     */
    public function getHealthyPercentage(): int
    {
        $total = $this->getTotalContentCount();

        if ($total === 0) {
            return 0;
        }

        $healthy = SeoMeta::query()
            ->where('seo_score', '>', 60)
            ->count();

        return (int) round(($healthy / $total) * 100);
    }

    /**
     * Get the grade label for a score.
     */
    public function getGradeForScore(int $score): string
    {
        return match (true) {
            $score <= 30 => __('filament-seo-pro::seo.grade_poor'),
            $score <= 60 => __('filament-seo-pro::seo.grade_fair'),
            $score <= 80 => __('filament-seo-pro::seo.grade_good'),
            default => __('filament-seo-pro::seo.grade_excellent'),
        };
    }

    /**
     * Get the color for a score.
     */
    public function getColorForScore(int $score): string
    {
        return match (true) {
            $score <= 30 => 'danger',
            $score <= 60 => 'warning',
            $score <= 80 => 'info',
            default => 'success',
        };
    }
}
