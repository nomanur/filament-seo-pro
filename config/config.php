<?php

/*
 * Filament SEO Pro Configuration
 *
 * Customize the thresholds and weights used by the SEO analysis engine.
 * All values below are sensible defaults based on industry best practices.
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Title Configuration
    |--------------------------------------------------------------------------
    */
    'title_length' => [
        'min' => 50,
        'max' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Meta Description Configuration
    |--------------------------------------------------------------------------
    */
    'description_length' => [
        'min' => 120,
        'max' => 160,
    ],

    /*
    |--------------------------------------------------------------------------
    | Content Configuration
    |--------------------------------------------------------------------------
    */
    'content' => [
        'min_word_count' => 300,
    ],

    /*
    |--------------------------------------------------------------------------
    | Readability Configuration
    |--------------------------------------------------------------------------
    */
    'readability' => [
        'target_sentence_length' => [
            'min' => 15,
            'max' => 20,
        ],
        'target_paragraph_sentences' => [
            'min' => 2,
            'max' => 4,
        ],
        'max_passive_voice_percentage' => 10.0,
        'min_transition_words' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Grade Thresholds
    |--------------------------------------------------------------------------
    | Score ranges that map to each SEO grade.
    */
    'grade_thresholds' => [
        'excellent' => 81,
        'good' => 61,
        'fair' => 31,
        // Anything below 'fair' is considered 'Poor'.
    ],

    /*
    |--------------------------------------------------------------------------
    | Check Weights
    |--------------------------------------------------------------------------
    | Weight assigned to each individual SEO check.
    | Higher weight = greater impact on the overall score.
    */
    'weights' => [
        'title_exists' => 10,
        'title_length' => 10,
        'keyword_in_title' => 10,
        'description_exists' => 10,
        'description_length' => 8,
        'keyword_in_description' => 8,
        'keyword_in_url' => 7,
        'content_length' => 10,
        'h1_exists' => 7,
        'h2_exists' => 5,
        'image_alt_text' => 5,
        'internal_links' => 5,
        'external_links' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Passive Voice Indicators
    |--------------------------------------------------------------------------
    | Common auxiliary + past participle patterns used to detect passive voice.
    */
    'passive_voice_auxiliaries' => [
        'is', 'are', 'was', 'were', 'be', 'been', 'being',
        'has been', 'have been', 'had been',
        'will be', 'will have been',
        'is being', 'are being', 'was being', 'were being',
        'gets', 'got', 'get',
    ],

    /*
    |--------------------------------------------------------------------------
    | Transition Words
    |--------------------------------------------------------------------------
    | Words / phrases that indicate good content flow and readability.
    */
    'transition_words' => [
        'additionally', 'also', 'moreover', 'furthermore', 'in addition',
        'however', 'nevertheless', 'nonetheless', 'on the other hand', 'conversely',
        'therefore', 'consequently', 'as a result', 'thus', 'hence',
        'for example', 'for instance', 'such as', 'specifically', 'in particular',
        'first', 'second', 'third', 'finally', 'next', 'then', 'meanwhile',
        'in conclusion', 'to summarize', 'in summary', 'overall', 'in short',
        'similarly', 'likewise', 'in the same way', 'equally',
        'although', 'even though', 'despite', 'in spite of', 'whereas',
        'because', 'since', 'due to', 'owing to', 'as a consequence',
        'in contrast', 'on the contrary', 'rather', 'instead',
        'above all', 'most importantly', 'indeed', 'in fact', 'certainly',
    ],

];
