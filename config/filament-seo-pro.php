<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Field Mappings
    |--------------------------------------------------------------------------
    |
    | These define which model fields are used as the source for SEO analysis.
    | Override per-resource using SeoTab::make()->contentField('body').
    |
    */

    'default_content_field' => 'content',
    'default_title_field' => 'title',
    'default_slug_field' => 'slug',

    /*
    |--------------------------------------------------------------------------
    | SEO Title Length
    |--------------------------------------------------------------------------
    |
    | The recommended character length range for SEO titles.
    | Google typically displays 50-60 characters.
    |
    */

    'title_length' => [
        'min' => 50,
        'max' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Meta Description Length
    |--------------------------------------------------------------------------
    |
    | The recommended character length range for meta descriptions.
    | Google typically displays 120-160 characters.
    |
    */

    'description_length' => [
        'min' => 120,
        'max' => 160,
    ],

    /*
    |--------------------------------------------------------------------------
    | Minimum Content Words
    |--------------------------------------------------------------------------
    |
    | The minimum number of words expected in content for a "pass" check.
    |
    */

    'min_content_words' => 300,

    /*
    |--------------------------------------------------------------------------
    | SEO Score Thresholds
    |--------------------------------------------------------------------------
    |
    | Score boundaries for each grade level.
    | poor: 0-30, fair: 31-60, good: 61-80, excellent: 81-100
    |
    */

    'score_thresholds' => [
        'poor' => 30,
        'fair' => 60,
        'good' => 80,
    ],

    /*
    |--------------------------------------------------------------------------
    | Translatable Support
    |--------------------------------------------------------------------------
    |
    | Enable support for spatie/laravel-translatable models.
    | When enabled, SEO fields will work with translatable attributes.
    |
    */

    'translatable' => false,

    /*
    |--------------------------------------------------------------------------
    | Cache TTL
    |--------------------------------------------------------------------------
    |
    | How long (in seconds) to cache SEO analysis results.
    | Set to 0 to disable caching.
    |
    */

    'cache_ttl' => 3600,

    /*
    |--------------------------------------------------------------------------
    | Queue Analysis
    |--------------------------------------------------------------------------
    |
    | When enabled, heavy SEO analysis is dispatched to a queue
    | instead of running synchronously during form interaction.
    |
    */

    'queue_analysis' => false,

    /*
    |--------------------------------------------------------------------------
    | Schema Types
    |--------------------------------------------------------------------------
    |
    | Available Schema.org types for the schema selector dropdown.
    |
    */

    'schema_types' => [
        'Article' => 'Article',
        'BlogPosting' => 'Blog Posting',
        'Product' => 'Product',
        'FAQPage' => 'FAQ',
        'Organization' => 'Organization',
        'Person' => 'Person',
        'LocalBusiness' => 'Local Business',
        'WebPage' => 'Web Page',
    ],

    /*
    |--------------------------------------------------------------------------
    | Robots Options
    |--------------------------------------------------------------------------
    |
    | Available robots meta tag directives.
    |
    */

    'robots_options' => [
        'index, follow' => 'Index, Follow (Default)',
        'noindex, follow' => 'No Index, Follow',
        'index, nofollow' => 'Index, No Follow',
        'noindex, nofollow' => 'No Index, No Follow',
    ],

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | Register models that use the HasSeo trait for bulk SEO management.
    | The SeoManagement page will query these models.
    |
    */

    'models' => [
        // \App\Models\Post::class,
        // \App\Models\Page::class,
    ],

];
