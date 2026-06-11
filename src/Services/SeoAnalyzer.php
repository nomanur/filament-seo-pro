<?php

declare(strict_types=1);

namespace Nomanur\FilamentSeoPro\Services;

use Nomanur\FilamentSeoPro\DTOs\SeoAnalysisResult;
use Nomanur\FilamentSeoPro\DTOs\SeoCheck;
use Nomanur\FilamentSeoPro\Enums\CheckStatus;

/**
 * Main SEO analysis service.
 *
 * Evaluates page-level SEO data against 13 industry-standard checks covering
 * titles, meta descriptions, keyword usage, content quality, headings, image
 * accessibility, and link structure.
 *
 * All thresholds and weights are read from the `filament-seo-pro` config so
 * consumers can customise behaviour without modifying source code.
 *
 * @example
 * ```php
 * $analyzer = new SeoAnalyzer();
 * $result   = $analyzer->analyze([
 *     'title'         => 'Best Coffee Shops in Dhaka',
 *     'description'   => 'Discover the top-rated coffee shops in Dhaka …',
 *     'focus_keyword' => 'coffee shops in Dhaka',
 *     'content'       => '<h1>…</h1><p>…</p>',
 *     'slug'          => 'best-coffee-shops-in-dhaka',
 *     'url'           => 'https://example.com/best-coffee-shops-in-dhaka',
 * ]);
 * ```
 */
class SeoAnalyzer
{
    /**
     * Run the full SEO analysis against the provided page data.
     *
     * @param  array{
     *     title?: string,
     *     description?: string,
     *     focus_keyword?: string,
     *     content?: string,
     *     slug?: string,
     *     url?: string,
     * }  $data  Associative array of page SEO data.
     * @return SeoAnalysisResult Immutable result containing all check outcomes, score, and grade.
     */
    public function analyze(array $data): SeoAnalysisResult
    {
        $title = trim((string) ($data['title'] ?? ''));
        $description = trim((string) ($data['description'] ?? ''));
        $keyword = trim((string) ($data['focus_keyword'] ?? ''));
        $content = trim((string) ($data['content'] ?? ''));
        $slug = trim((string) ($data['slug'] ?? ''));
        $url = trim((string) ($data['url'] ?? ''));
        $plainContent = $this->stripHtml($content);

        $checks = [
            $this->checkTitleExists($title),
            $this->checkTitleLength($title),
            $this->checkKeywordInTitle($title, $keyword),
            $this->checkDescriptionExists($description),
            $this->checkDescriptionLength($description),
            $this->checkKeywordInDescription($description, $keyword),
            $this->checkKeywordInUrl($slug, $url, $keyword),
            $this->checkContentLength($plainContent),
            $this->checkH1Exists($content),
            $this->checkH2Exists($content),
            $this->checkImageAltText($content),
            $this->checkInternalLinks($content),
            $this->checkExternalLinks($content),
        ];

        $calculator = new SeoScoreCalculator;
        $scoring = $calculator->calculate(new SeoAnalysisResult(checks: $checks, score: 0, grade: ''));

        return new SeoAnalysisResult(
            checks: $checks,
            score: $scoring['score'],
            grade: $scoring['grade'],
        );
    }

    // =========================================================================
    // Title Checks
    // =========================================================================

    /**
     * Check 1 — Title exists and is non-empty.
     */
    protected function checkTitleExists(string $title): SeoCheck
    {
        $weight = $this->weight('title_exists');

        if ($title === '') {
            return new SeoCheck(
                key: 'title_exists',
                label: 'Title Exists',
                status: CheckStatus::Fail,
                message: 'No title has been set. Add a descriptive page title.',
                category: 'Title',
                weight: $weight,
            );
        }

        return new SeoCheck(
            key: 'title_exists',
            label: 'Title Exists',
            status: CheckStatus::Pass,
            message: 'A page title is set.',
            category: 'Title',
            weight: $weight,
        );
    }

    /**
     * Check 2 — Title length is within the recommended 50-60 character range.
     */
    protected function checkTitleLength(string $title): SeoCheck
    {
        $weight = $this->weight('title_length');
        $length = mb_strlen($title);
        $minLen = (int) config('filament-seo-pro.title_length.min', 50);
        $maxLen = (int) config('filament-seo-pro.title_length.max', 60);

        if ($title === '') {
            return new SeoCheck(
                key: 'title_length',
                label: 'Title Length',
                status: CheckStatus::Fail,
                message: "Title is empty. Aim for {$minLen}–{$maxLen} characters.",
                category: 'Title',
                weight: $weight,
            );
        }

        if ($length >= $minLen && $length <= $maxLen) {
            return new SeoCheck(
                key: 'title_length',
                label: 'Title Length',
                status: CheckStatus::Pass,
                message: "Title is {$length} characters — within the recommended {$minLen}–{$maxLen} range.",
                category: 'Title',
                weight: $weight,
            );
        }

        // Close to range → warning; far outside → fail
        $tolerance = 10;
        $isClose = ($length >= $minLen - $tolerance && $length < $minLen)
                  || ($length > $maxLen && $length <= $maxLen + $tolerance);

        return new SeoCheck(
            key: 'title_length',
            label: 'Title Length',
            status: $isClose ? CheckStatus::Warn : CheckStatus::Fail,
            message: "Title is {$length} characters. Aim for {$minLen}–{$maxLen} characters.",
            category: 'Title',
            weight: $weight,
        );
    }

    /**
     * Check 3 — Focus keyword appears in the title.
     */
    protected function checkKeywordInTitle(string $title, string $keyword): SeoCheck
    {
        $weight = $this->weight('keyword_in_title');

        if ($keyword === '') {
            return new SeoCheck(
                key: 'keyword_in_title',
                label: 'Keyword in Title',
                status: CheckStatus::Warn,
                message: 'No focus keyword has been set. Add a focus keyword to enable this check.',
                category: 'Title',
                weight: $weight,
            );
        }

        if ($this->containsKeyword($title, $keyword)) {
            return new SeoCheck(
                key: 'keyword_in_title',
                label: 'Keyword in Title',
                status: CheckStatus::Pass,
                message: "The focus keyword \"{$keyword}\" appears in the title.",
                category: 'Title',
                weight: $weight,
            );
        }

        return new SeoCheck(
            key: 'keyword_in_title',
            label: 'Keyword in Title',
            status: CheckStatus::Fail,
            message: "The focus keyword \"{$keyword}\" does not appear in the title.",
            category: 'Title',
            weight: $weight,
        );
    }

    // =========================================================================
    // Meta Description Checks
    // =========================================================================

    /**
     * Check 4 — Meta description exists and is non-empty.
     */
    protected function checkDescriptionExists(string $description): SeoCheck
    {
        $weight = $this->weight('description_exists');

        if ($description === '') {
            return new SeoCheck(
                key: 'description_exists',
                label: 'Meta Description Exists',
                status: CheckStatus::Fail,
                message: 'No meta description has been set. Add a compelling meta description.',
                category: 'Meta Description',
                weight: $weight,
            );
        }

        return new SeoCheck(
            key: 'description_exists',
            label: 'Meta Description Exists',
            status: CheckStatus::Pass,
            message: 'A meta description is set.',
            category: 'Meta Description',
            weight: $weight,
        );
    }

    /**
     * Check 5 — Meta description length is within the recommended 120-160 character range.
     */
    protected function checkDescriptionLength(string $description): SeoCheck
    {
        $weight = $this->weight('description_length');
        $length = mb_strlen($description);
        $minLen = (int) config('filament-seo-pro.description_length.min', 120);
        $maxLen = (int) config('filament-seo-pro.description_length.max', 160);

        if ($description === '') {
            return new SeoCheck(
                key: 'description_length',
                label: 'Description Length',
                status: CheckStatus::Fail,
                message: "Meta description is empty. Aim for {$minLen}–{$maxLen} characters.",
                category: 'Meta Description',
                weight: $weight,
            );
        }

        if ($length >= $minLen && $length <= $maxLen) {
            return new SeoCheck(
                key: 'description_length',
                label: 'Description Length',
                status: CheckStatus::Pass,
                message: "Meta description is {$length} characters — within the recommended {$minLen}–{$maxLen} range.",
                category: 'Meta Description',
                weight: $weight,
            );
        }

        $tolerance = 20;
        $isClose = ($length >= $minLen - $tolerance && $length < $minLen)
                  || ($length > $maxLen && $length <= $maxLen + $tolerance);

        return new SeoCheck(
            key: 'description_length',
            label: 'Description Length',
            status: $isClose ? CheckStatus::Warn : CheckStatus::Fail,
            message: "Meta description is {$length} characters. Aim for {$minLen}–{$maxLen} characters.",
            category: 'Meta Description',
            weight: $weight,
        );
    }

    /**
     * Check 6 — Focus keyword appears in the meta description.
     */
    protected function checkKeywordInDescription(string $description, string $keyword): SeoCheck
    {
        $weight = $this->weight('keyword_in_description');

        if ($keyword === '') {
            return new SeoCheck(
                key: 'keyword_in_description',
                label: 'Keyword in Description',
                status: CheckStatus::Warn,
                message: 'No focus keyword has been set. Add a focus keyword to enable this check.',
                category: 'Meta Description',
                weight: $weight,
            );
        }

        if ($this->containsKeyword($description, $keyword)) {
            return new SeoCheck(
                key: 'keyword_in_description',
                label: 'Keyword in Description',
                status: CheckStatus::Pass,
                message: "The focus keyword \"{$keyword}\" appears in the meta description.",
                category: 'Meta Description',
                weight: $weight,
            );
        }

        return new SeoCheck(
            key: 'keyword_in_description',
            label: 'Keyword in Description',
            status: CheckStatus::Fail,
            message: "The focus keyword \"{$keyword}\" does not appear in the meta description.",
            category: 'Meta Description',
            weight: $weight,
        );
    }

    // =========================================================================
    // URL / Slug Check
    // =========================================================================

    /**
     * Check 7 — Focus keyword appears in the URL or slug.
     */
    protected function checkKeywordInUrl(string $slug, string $url, string $keyword): SeoCheck
    {
        $weight = $this->weight('keyword_in_url');

        if ($keyword === '') {
            return new SeoCheck(
                key: 'keyword_in_url',
                label: 'Keyword in URL',
                status: CheckStatus::Warn,
                message: 'No focus keyword has been set. Add a focus keyword to enable this check.',
                category: 'URL',
                weight: $weight,
            );
        }

        // Normalise keyword to a slug-friendly form for comparison
        $keywordSlug = $this->keywordToSlug($keyword);
        $target = $slug !== '' ? $slug : $url;

        if ($target !== '' && $this->slugContainsKeyword($target, $keywordSlug)) {
            return new SeoCheck(
                key: 'keyword_in_url',
                label: 'Keyword in URL',
                status: CheckStatus::Pass,
                message: "The focus keyword \"{$keyword}\" appears in the URL.",
                category: 'URL',
                weight: $weight,
            );
        }

        return new SeoCheck(
            key: 'keyword_in_url',
            label: 'Keyword in URL',
            status: CheckStatus::Fail,
            message: "The focus keyword \"{$keyword}\" does not appear in the URL.",
            category: 'URL',
            weight: $weight,
        );
    }

    // =========================================================================
    // Content Checks
    // =========================================================================

    /**
     * Check 8 — Content has a minimum number of words (default: 300).
     */
    protected function checkContentLength(string $plainContent): SeoCheck
    {
        $weight = $this->weight('content_length');
        $minWords = (int) config('filament-seo-pro.content.min_word_count', 300);
        $wordCount = $this->wordCount($plainContent);

        if ($wordCount >= $minWords) {
            return new SeoCheck(
                key: 'content_length',
                label: 'Content Length',
                status: CheckStatus::Pass,
                message: "Content has {$wordCount} words — meets the minimum of {$minWords}.",
                category: 'Content',
                weight: $weight,
            );
        }

        // More than half the minimum → warning; otherwise → fail
        $status = $wordCount >= (int) ($minWords / 2) ? CheckStatus::Warn : CheckStatus::Fail;

        return new SeoCheck(
            key: 'content_length',
            label: 'Content Length',
            status: $status,
            message: "Content has only {$wordCount} words. Aim for at least {$minWords} words.",
            category: 'Content',
            weight: $weight,
        );
    }

    /**
     * Check 9 — An H1 heading exists in the content.
     */
    protected function checkH1Exists(string $htmlContent): SeoCheck
    {
        $weight = $this->weight('h1_exists');
        $hasH1 = (bool) preg_match('/<h1[\s>]/i', $htmlContent);

        return new SeoCheck(
            key: 'h1_exists',
            label: 'H1 Heading Exists',
            status: $hasH1 ? CheckStatus::Pass : CheckStatus::Fail,
            message: $hasH1
                ? 'An H1 heading is present in the content.'
                : 'No H1 heading found. Add a primary heading to your content.',
            category: 'Content',
            weight: $weight,
        );
    }

    /**
     * Check 10 — At least one H2 heading exists in the content.
     */
    protected function checkH2Exists(string $htmlContent): SeoCheck
    {
        $weight = $this->weight('h2_exists');
        $hasH2 = (bool) preg_match('/<h2[\s>]/i', $htmlContent);

        return new SeoCheck(
            key: 'h2_exists',
            label: 'H2 Heading Exists',
            status: $hasH2 ? CheckStatus::Pass : CheckStatus::Warn,
            message: $hasH2
                ? 'At least one H2 sub-heading is present.'
                : 'No H2 sub-heading found. Consider adding sub-headings to structure your content.',
            category: 'Content',
            weight: $weight,
        );
    }

    /**
     * Check 11 — All images have alt text attributes.
     */
    protected function checkImageAltText(string $htmlContent): SeoCheck
    {
        $weight = $this->weight('image_alt_text');

        // Find all <img> tags
        preg_match_all('/<img\b[^>]*>/i', $htmlContent, $imgMatches);
        $images = $imgMatches[0];

        if ($images === []) {
            return new SeoCheck(
                key: 'image_alt_text',
                label: 'Image Alt Text',
                status: CheckStatus::Warn,
                message: 'No images found in the content. Consider adding relevant images with descriptive alt text.',
                category: 'Content',
                weight: $weight,
            );
        }

        $missingAlt = 0;

        foreach ($images as $imgTag) {
            // Missing alt attribute entirely, or alt=""
            if (! preg_match('/\balt\s*=\s*"[^"]+"/i', $imgTag)
                && ! preg_match("/\balt\s*=\s*'[^']+'/i", $imgTag)) {
                $missingAlt++;
            }
        }

        $totalImages = count($images);

        if ($missingAlt === 0) {
            return new SeoCheck(
                key: 'image_alt_text',
                label: 'Image Alt Text',
                status: CheckStatus::Pass,
                message: "All {$totalImages} image(s) have descriptive alt text.",
                category: 'Content',
                weight: $weight,
            );
        }

        $status = $missingAlt < $totalImages ? CheckStatus::Warn : CheckStatus::Fail;

        return new SeoCheck(
            key: 'image_alt_text',
            label: 'Image Alt Text',
            status: $status,
            message: "{$missingAlt} of {$totalImages} image(s) are missing alt text. Add descriptive alt attributes.",
            category: 'Content',
            weight: $weight,
        );
    }

    // =========================================================================
    // Link Checks
    // =========================================================================

    /**
     * Check 12 — At least one internal link is present in the content.
     *
     * An "internal link" is any anchor whose href starts with `/` (relative)
     * or matches the site's own domain (when a full URL is provided in $data).
     */
    protected function checkInternalLinks(string $htmlContent): SeoCheck
    {
        $weight = $this->weight('internal_links');

        preg_match_all('/<a\b[^>]*\bhref\s*=\s*["\']([^"\']+)["\'][^>]*>/i', $htmlContent, $matches);
        $hrefs = $matches[1];

        $internalCount = 0;

        foreach ($hrefs as $href) {
            $href = trim($href);

            // Relative URLs (starting with / but not //) are internal
            if (str_starts_with($href, '/') && ! str_starts_with($href, '//')) {
                $internalCount++;

                continue;
            }

            // Anchors within the same page
            if (str_starts_with($href, '#')) {
                $internalCount++;
            }
        }

        if ($internalCount > 0) {
            return new SeoCheck(
                key: 'internal_links',
                label: 'Internal Links',
                status: CheckStatus::Pass,
                message: "Found {$internalCount} internal link(s) in the content.",
                category: 'Links',
                weight: $weight,
            );
        }

        return new SeoCheck(
            key: 'internal_links',
            label: 'Internal Links',
            status: CheckStatus::Fail,
            message: 'No internal links found. Add links to other pages on your site.',
            category: 'Links',
            weight: $weight,
        );
    }

    /**
     * Check 13 — At least one external link is present in the content.
     *
     * An "external link" is any anchor whose href starts with `http://` or
     * `https://` and is not an internal / relative link.
     */
    protected function checkExternalLinks(string $htmlContent): SeoCheck
    {
        $weight = $this->weight('external_links');

        preg_match_all('/<a\b[^>]*\bhref\s*=\s*["\']([^"\']+)["\'][^>]*>/i', $htmlContent, $matches);
        $hrefs = $matches[1];

        $externalCount = 0;

        foreach ($hrefs as $href) {
            $href = trim($href);

            if (preg_match('#^https?://#i', $href)) {
                $externalCount++;
            }
        }

        if ($externalCount > 0) {
            return new SeoCheck(
                key: 'external_links',
                label: 'External Links',
                status: CheckStatus::Pass,
                message: "Found {$externalCount} external link(s) in the content.",
                category: 'Links',
                weight: $weight,
            );
        }

        return new SeoCheck(
            key: 'external_links',
            label: 'External Links',
            status: CheckStatus::Warn,
            message: 'No external links found. Consider linking to authoritative external sources.',
            category: 'Links',
            weight: $weight,
        );
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    /**
     * Retrieve the configured weight for a given check key.
     */
    protected function weight(string $key): int
    {
        return (int) config("filament-seo-pro.weights.{$key}", 5);
    }

    /**
     * Check whether $haystack contains the $keyword (case-insensitive).
     */
    protected function containsKeyword(string $haystack, string $keyword): bool
    {
        return mb_stripos($haystack, $keyword) !== false;
    }

    /**
     * Convert a focus keyword into a slug-friendly string for URL comparison.
     *
     * Example: "coffee shops in Dhaka" → "coffee-shops-in-dhaka"
     */
    protected function keywordToSlug(string $keyword): string
    {
        $slug = mb_strtolower($keyword);
        $slug = (string) preg_replace('/[^\p{L}\p{N}\s-]/u', '', $slug);
        $slug = (string) preg_replace('/[\s-]+/', '-', $slug);

        return trim($slug, '-');
    }

    /**
     * Determine whether a slug / URL contains the keyword slug.
     */
    protected function slugContainsKeyword(string $target, string $keywordSlug): bool
    {
        $normalisedTarget = mb_strtolower($target);

        return str_contains($normalisedTarget, $keywordSlug);
    }

    /**
     * Strip all HTML tags and decode entities, returning plain text.
     */
    protected function stripHtml(string $html): string
    {
        // Remove script and style blocks entirely
        $text = (string) preg_replace('#<(script|style)\b[^>]*>.*?</\1>#si', '', $html);

        // Strip remaining tags
        $text = strip_tags($text);

        // Decode HTML entities
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Normalise whitespace
        $text = (string) preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }

    /**
     * Count the number of words in a plain-text string.
     */
    protected function wordCount(string $text): int
    {
        if (trim($text) === '') {
            return 0;
        }

        // Split on whitespace and count non-empty tokens
        $words = preg_split('/\s+/', trim($text), -1, PREG_SPLIT_NO_EMPTY);

        return $words !== false ? count($words) : 0;
    }
}
