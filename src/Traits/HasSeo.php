<?php

declare(strict_types=1);

namespace Nomanur\FilamentSeoPro\Traits;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use Nomanur\FilamentSeoPro\Models\SeoMeta;

/**
 * Add SEO metadata capabilities to any Eloquent model.
 *
 * Usage:
 *
 *     class Post extends Model
 *     {
 *         use HasSeo;
 *     }
 *
 *     $post->seo;              // Access SEO meta
 *     $post->getOrCreateSeo(); // Get or create SEO meta
 *
 * @property-read SeoMeta|null $seo
 */
trait HasSeo
{
    /**
     * Boot the HasSeo trait.
     *
     * Automatically cascades deletion of SEO meta when the parent model is deleted.
     */
    public static function bootHasSeo(): void
    {
        static::deleting(function (self $model): void {
            $model->seo?->delete();
        });
    }

    /**
     * Get the SEO metadata for this model.
     *
     * @return MorphOne<SeoMeta, $this>
     */
    public function seo(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }

    /**
     * Get the existing SEO meta or create a new one.
     */
    public function getOrCreateSeo(): SeoMeta
    {
        return $this->seo ?? $this->seo()->create([
            'robots' => 'index, follow',
        ]);
    }

    /**
     * Update the SEO score for this model.
     */
    public function updateSeoScore(int $score): void
    {
        $this->getOrCreateSeo()->update(['seo_score' => $score]);
    }

    /**
     * Check if this model has SEO meta configured.
     */
    public function hasSeoMeta(): bool
    {
        return $this->seo !== null && (
            $this->seo->title !== null ||
            $this->seo->description !== null
        );
    }

    /**
     * Get the SEO data as an array suitable for the analyzer.
     *
     * @return array<string, mixed>
     */
    public function getSeoAnalysisData(): array
    {
        $contentField = config('filament-seo-pro.default_content_field', 'content');
        $titleField = config('filament-seo-pro.default_title_field', 'title');
        $slugField = config('filament-seo-pro.default_slug_field', 'slug');

        $seo = $this->seo;

        return [
            'title' => $seo?->title ?? $this->getAttribute($titleField) ?? '',
            'description' => $seo?->description ?? '',
            'focus_keyword' => $seo?->focus_keyword ?? '',
            'content' => $this->getAttribute($contentField) ?? '',
            'slug' => $this->getAttribute($slugField) ?? '',
            'url' => method_exists($this, 'getUrl') ? $this->getUrl() : '',
        ];
    }
}
