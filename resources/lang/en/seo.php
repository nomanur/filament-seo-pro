<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Basic Field Labels, Placeholders and Helpers
    |--------------------------------------------------------------------------
    */

    'seo_title' => 'SEO Title',
    'seo_title_placeholder' => 'Enter SEO title...',
    'title_helper_empty' => 'No SEO title set. The search engine preview will fallback to the record title.',
    'title_helper_short' => 'The SEO title is too short (:current characters). Minimum recommended length is :min characters.',
    'title_helper_long' => 'The SEO title is too long (:current characters). Maximum recommended length is :max characters.',
    'title_helper_optimal' => 'The SEO title length is optimal.',

    'meta_description' => 'Meta Description',
    'meta_description_placeholder' => 'Enter meta description...',
    'description_helper_empty' => 'No meta description set. Search engines will automatically determine the description.',
    'description_helper_short' => 'The meta description is too short (:current characters). Minimum recommended length is :min characters.',
    'description_helper_long' => 'The meta description is too long (:current characters). Maximum recommended length is :max characters.',
    'description_helper_optimal' => 'The meta description length is optimal.',

    'focus_keyword' => 'Focus Keyword',
    'focus_keyword_placeholder' => 'Enter focus keyword...',
    'focus_keyword_helper' => 'Define the primary keyword this page aims to rank for.',

    'keywords' => 'Keywords',
    'keywords_placeholder' => 'Enter keywords separated by commas...',
    'keywords_helper' => 'Meta keywords are mostly ignored by modern search engines but can be used for internal search or indexing.',

    'canonical_url' => 'Canonical URL',
    'robots' => 'Robots Meta',

    'schema_type' => 'Schema Type',
    'schema_type_placeholder' => 'Select schema type...',
    'schema_type_helper' => 'Define the schema.org structured data type for this page.',

    'open_graph' => 'Open Graph (Facebook)',
    'open_graph_description' => 'Configure how your page appears when shared on Facebook, LinkedIn, and other social media platforms.',
    'og_title' => 'OG Title',
    'og_title_placeholder' => 'Enter Open Graph title...',
    'og_description' => 'OG Description',
    'og_description_placeholder' => 'Enter Open Graph description...',
    'og_image' => 'OG Image',
    'og_image_helper' => 'Recommended size: 1200x630 pixels. Aspect ratio: 1.91:1.',
    'og_preview' => 'Facebook Preview',

    'twitter_card' => 'Twitter Card',
    'twitter_card_description' => 'Configure how your page appears when shared on Twitter / X.',
    'twitter_title' => 'Twitter Title',
    'twitter_title_placeholder' => 'Enter Twitter title...',
    'twitter_description' => 'Twitter Description',
    'twitter_description_placeholder' => 'Enter Twitter description...',
    'twitter_image' => 'Twitter Image',
    'twitter_image_helper' => 'Recommended size: 1200x600 pixels. Aspect ratio: 2:1.',
    'twitter_preview' => 'Twitter / X Preview',

    'schema_markup' => 'Schema Markup',
    'schema_markup_description' => 'Structured data markup for search engines to better understand and display your content in search results.',
    'schema_info' => 'Structured Data Info',
    'schema_info_helper' => 'Configure JSON-LD schema markup below to enhance your search engine appearance.',

    'section_label' => 'Search Engine Optimization',
    'section_description' => 'Optimize your page\'s visibility in search results by configuring titles, descriptions, and social previews.',
    'tab_label' => 'SEO',
    'seo' => 'SEO',
    'meta_settings' => 'Meta Settings',
    'meta_settings_description' => 'Configure basic SEO settings including title, description, keywords, canonical URLs, and robots directives.',

    /*
    |--------------------------------------------------------------------------
    | Field Labels
    |--------------------------------------------------------------------------
    */

    'fields' => [
        'seo_title' => 'SEO Title',
        'meta_description' => 'Meta Description',
        'focus_keyword' => 'Focus Keyword',
        'slug' => 'URL Slug',
        'canonical_url' => 'Canonical URL',
        'robots' => 'Robots Meta',
        'og_title' => 'OG Title',
        'og_description' => 'OG Description',
        'og_image' => 'OG Image',
        'twitter_title' => 'Twitter Title',
        'twitter_description' => 'Twitter Description',
        'twitter_image' => 'Twitter Image',
        'twitter_card_type' => 'Twitter Card Type',
    ],

    /*
    |--------------------------------------------------------------------------
    | Section Headings
    |--------------------------------------------------------------------------
    */

    'sections' => [
        'seo' => 'Search Engine Optimization',
        'general' => 'General SEO',
        'social' => 'Social Media',
        'open_graph' => 'Open Graph (Facebook)',
        'twitter' => 'Twitter / X Card',
        'advanced' => 'Advanced SEO',
        'analysis' => 'SEO Analysis',
        'preview' => 'Search Preview',
        'score' => 'SEO Score',
        'checklist' => 'SEO Checklist',
        'readability' => 'Readability',
    ],

    /*
    |--------------------------------------------------------------------------
    | Grade Labels
    |--------------------------------------------------------------------------
    */

    'grade' => [
        'excellent' => 'Excellent',
        'good' => 'Good',
        'needs_work' => 'Needs Work',
        'poor' => 'Poor',
    ],

    /*
    |--------------------------------------------------------------------------
    | Category Labels
    |--------------------------------------------------------------------------
    */

    'category' => [
        'title' => 'Title',
        'description' => 'Description',
        'url' => 'URL',
        'content' => 'Content',
        'links' => 'Links',
    ],

    /*
    |--------------------------------------------------------------------------
    | Preview Strings
    |--------------------------------------------------------------------------
    */

    'preview' => [
        'untitled' => 'Untitled Page',
        'no_description' => 'No description has been set for this page. Add a meta description to improve search engine visibility.',
        'google_heading' => 'Google Preview',
        'og_heading' => 'Facebook Preview',
        'twitter_heading' => 'Twitter / X Preview',
    ],

    /*
    |--------------------------------------------------------------------------
    | Checklist
    |--------------------------------------------------------------------------
    */

    'checklist' => [
        'empty' => 'Enter a focus keyword and content to see SEO analysis results.',
    ],

    /*
    |--------------------------------------------------------------------------
    | SEO Check Messages
    |--------------------------------------------------------------------------
    |
    | Each check has pass, warn, and fail message variants.
    |
    */

    'checks' => [

        // 1. Title contains focus keyword
        'title_keyword' => [
            'label' => 'Keyword in title',
            'pass' => 'The focus keyword appears in the SEO title. Great!',
            'warn' => 'The focus keyword appears in the title but not at the beginning.',
            'fail' => 'The focus keyword does not appear in the SEO title.',
        ],

        // 2. Title length
        'title_length' => [
            'label' => 'Title length',
            'pass' => 'The title length is between 30 and 60 characters. Perfect!',
            'warn' => 'The title is a bit short or long. Aim for 30–60 characters.',
            'fail' => 'The title is too short or too long. Optimal length is 30–60 characters.',
        ],

        // 3. Description contains focus keyword
        'description_keyword' => [
            'label' => 'Keyword in description',
            'pass' => 'The focus keyword appears in the meta description.',
            'warn' => 'The focus keyword appears late in the meta description.',
            'fail' => 'The focus keyword does not appear in the meta description.',
        ],

        // 4. Description length
        'description_length' => [
            'label' => 'Description length',
            'pass' => 'The meta description length is between 120 and 160 characters.',
            'warn' => 'The meta description is slightly outside the ideal range (120–160 characters).',
            'fail' => 'The meta description is too short or too long. Aim for 120–160 characters.',
        ],

        // 5. Slug contains focus keyword
        'slug_keyword' => [
            'label' => 'Keyword in URL',
            'pass' => 'The focus keyword appears in the URL slug.',
            'warn' => 'The URL slug partially contains the focus keyword.',
            'fail' => 'The focus keyword does not appear in the URL slug.',
        ],

        // 6. Slug length
        'slug_length' => [
            'label' => 'URL length',
            'pass' => 'The URL slug is concise and SEO-friendly.',
            'warn' => 'The URL slug is a bit long. Consider shortening it.',
            'fail' => 'The URL slug is too long. Keep it under 75 characters.',
        ],

        // 7. Content length
        'content_length' => [
            'label' => 'Content length',
            'pass' => 'The content has a good length (300+ words).',
            'warn' => 'The content is somewhat short (150–300 words). Consider adding more.',
            'fail' => 'The content is too short (under 150 words). Aim for at least 300 words.',
        ],

        // 8. Keyword density
        'keyword_density' => [
            'label' => 'Keyword density',
            'pass' => 'The keyword density is within the optimal range (1–3%).',
            'warn' => 'The keyword density is slightly outside the ideal range.',
            'fail' => 'The keyword density is too low or too high. Aim for 1–3%.',
        ],

        // 9. Keyword in first paragraph
        'keyword_in_intro' => [
            'label' => 'Keyword in introduction',
            'pass' => 'The focus keyword appears in the first paragraph.',
            'warn' => 'The focus keyword appears but not prominently in the introduction.',
            'fail' => 'The focus keyword does not appear in the first paragraph.',
        ],

        // 10. Heading contains keyword
        'heading_keyword' => [
            'label' => 'Keyword in headings',
            'pass' => 'The focus keyword appears in at least one subheading.',
            'warn' => 'The subheadings partially contain the focus keyword.',
            'fail' => 'The focus keyword does not appear in any subheading.',
        ],

        // 11. Image alt text
        'image_alt' => [
            'label' => 'Image alt attributes',
            'pass' => 'All images have alt text, and at least one contains the keyword.',
            'warn' => 'Some images are missing alt text or the keyword is not present.',
            'fail' => 'Images are missing alt text. Add descriptive alt attributes.',
        ],

        // 12. Internal links
        'internal_links' => [
            'label' => 'Internal links',
            'pass' => 'The content contains internal links. Well done!',
            'warn' => 'Only a few internal links found. Consider adding more.',
            'fail' => 'No internal links found. Add links to other pages on your site.',
        ],

        // 13. External links
        'external_links' => [
            'label' => 'External links',
            'pass' => 'The content contains outbound links to relevant sources.',
            'warn' => 'Very few external links. Consider referencing authoritative sources.',
            'fail' => 'No external links found. Link to relevant, authoritative sources.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Widget Labels
    |--------------------------------------------------------------------------
    */

    'widget' => [
        'title' => 'SEO Overview',
        'avg_score' => 'Avg. Score',
        'pages_analyzed' => 'Pages Analyzed',
        'issues_found' => 'Issues Found',
        'top_issues' => 'Top Issues',
        'recent_changes' => 'Recent Changes',
    ],

    /*
    |--------------------------------------------------------------------------
    | Analyzer Panel
    |--------------------------------------------------------------------------
    */

    'analyzer' => [
        'analyzing' => 'Analyzing content...',
        'no_data' => 'No content to analyze yet.',
        'refresh' => 'Re-analyze',
    ],
];
