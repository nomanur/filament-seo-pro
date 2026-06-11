<?php

declare(strict_types=1);

namespace Nomanur\FilamentSeoPro\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * SEO metadata model stored via polymorphic relationship.
 *
 * @property int $id
 * @property string $seoable_type
 * @property int $seoable_id
 * @property string|null $title
 * @property string|null $description
 * @property string|null $keywords
 * @property string|null $focus_keyword
 * @property string|null $canonical_url
 * @property string $robots
 * @property string|null $og_title
 * @property string|null $og_description
 * @property string|null $og_image
 * @property string|null $twitter_title
 * @property string|null $twitter_description
 * @property string|null $twitter_image
 * @property string|null $schema_type
 * @property int $seo_score
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model $seoable
 */
class SeoMeta extends Model
{
    protected $attributes = [
        'robots' => 'index, follow',
        'seo_score' => 0,
    ];

    /**
     * Get the fillable attributes for the model.
     */
    protected $table = 'seo_meta';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'seoable_type',
        'seoable_id',
        'title',
        'description',
        'keywords',
        'focus_keyword',
        'canonical_url',
        'robots',
        'og_title',
        'og_description',
        'og_image',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'schema_type',
        'seo_score',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'seo_score' => 'integer',
        ];
    }

    /**
     * Get the parent seoable model (Post, Page, Product, etc.).
     */
    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }
}
