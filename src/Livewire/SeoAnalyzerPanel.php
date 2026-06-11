<?php

namespace Nomanur\FilamentSeoPro\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Nomanur\FilamentSeoPro\Analyzers\ReadabilityAnalyzer;
use Nomanur\FilamentSeoPro\Analyzers\SeoAnalyzer;

class SeoAnalyzerPanel extends Component
{
    public string $title = '';

    public string $description = '';

    public string $focusKeyword = '';

    public string $content = '';

    public string $slug = '';

    public string $url = '';

    public array $analysisResult = [];

    public array $readabilityResult = [];

    /**
     * Listen for SEO data updates from the form.
     */
    #[On('seo-data-updated')]
    public function updateSeoData(
        string $title = '',
        string $description = '',
        string $focusKeyword = '',
        string $content = '',
        string $slug = '',
        string $url = ''
    ): void {
        $this->title = $title;
        $this->description = $description;
        $this->focusKeyword = $focusKeyword;
        $this->content = $content;
        $this->slug = $slug;
        $this->url = $url;

        $this->analyzeContent();
    }

    /**
     * Run SEO and readability analysis on the current content.
     */
    public function analyzeContent(): void
    {
        if (empty($this->focusKeyword) && empty($this->content)) {
            $this->analysisResult = [];
            $this->readabilityResult = [];

            return;
        }

        // Run SEO Analyzer
        if (class_exists(SeoAnalyzer::class)) {
            $seoAnalyzer = new SeoAnalyzer(
                title: $this->title,
                description: $this->description,
                focusKeyword: $this->focusKeyword,
                content: $this->content,
                slug: $this->slug,
                url: $this->url,
            );

            $this->analysisResult = $seoAnalyzer->analyze();
        } else {
            $this->analysisResult = $this->getPlaceholderAnalysis();
        }

        // Run Readability Analyzer
        if (class_exists(ReadabilityAnalyzer::class)) {
            $readabilityAnalyzer = new ReadabilityAnalyzer(
                content: $this->content,
            );

            $this->readabilityResult = $readabilityAnalyzer->analyze();
        } else {
            $this->readabilityResult = [];
        }

        $this->dispatch('seo-analysis-complete', [
            'score' => $this->getScore(),
            'checks' => $this->getChecks(),
        ]);
    }

    /**
     * Get the overall SEO score.
     */
    public function getScore(): int
    {
        return $this->analysisResult['score'] ?? 0;
    }

    /**
     * Get the individual check results.
     */
    public function getChecks(): array
    {
        return $this->analysisResult['checks'] ?? [];
    }

    /**
     * Get readability score.
     */
    public function getReadabilityScore(): int
    {
        return $this->readabilityResult['score'] ?? 0;
    }

    /**
     * Provide placeholder analysis when analyzers are not yet available.
     */
    protected function getPlaceholderAnalysis(): array
    {
        $checks = [];
        $score = 0;
        $totalChecks = 0;

        // Title checks
        if (! empty($this->title)) {
            $titleLen = mb_strlen($this->title);
            $titleHasKeyword = ! empty($this->focusKeyword) &&
                str_contains(mb_strtolower($this->title), mb_strtolower($this->focusKeyword));

            $checks[] = [
                'key' => 'title_keyword',
                'category' => 'title',
                'label' => __('filament-seo-pro::seo.checks.title_keyword.label'),
                'status' => $titleHasKeyword ? 'pass' : 'fail',
                'message' => $titleHasKeyword
                    ? __('filament-seo-pro::seo.checks.title_keyword.pass')
                    : __('filament-seo-pro::seo.checks.title_keyword.fail'),
            ];
            $score += $titleHasKeyword ? 1 : 0;
            $totalChecks++;

            $titleLenStatus = ($titleLen >= 30 && $titleLen <= 60) ? 'pass' : (($titleLen >= 20 && $titleLen <= 70) ? 'warn' : 'fail');
            $checks[] = [
                'key' => 'title_length',
                'category' => 'title',
                'label' => __('filament-seo-pro::seo.checks.title_length.label'),
                'status' => $titleLenStatus,
                'message' => __("filament-seo-pro::seo.checks.title_length.{$titleLenStatus}"),
            ];
            $score += $titleLenStatus === 'pass' ? 1 : ($titleLenStatus === 'warn' ? 0.5 : 0);
            $totalChecks++;
        }

        // Description checks
        if (! empty($this->description)) {
            $descLen = mb_strlen($this->description);
            $descHasKeyword = ! empty($this->focusKeyword) &&
                str_contains(mb_strtolower($this->description), mb_strtolower($this->focusKeyword));

            $checks[] = [
                'key' => 'description_keyword',
                'category' => 'description',
                'label' => __('filament-seo-pro::seo.checks.description_keyword.label'),
                'status' => $descHasKeyword ? 'pass' : 'fail',
                'message' => $descHasKeyword
                    ? __('filament-seo-pro::seo.checks.description_keyword.pass')
                    : __('filament-seo-pro::seo.checks.description_keyword.fail'),
            ];
            $score += $descHasKeyword ? 1 : 0;
            $totalChecks++;

            $descLenStatus = ($descLen >= 120 && $descLen <= 160) ? 'pass' : (($descLen >= 80 && $descLen <= 200) ? 'warn' : 'fail');
            $checks[] = [
                'key' => 'description_length',
                'category' => 'description',
                'label' => __('filament-seo-pro::seo.checks.description_length.label'),
                'status' => $descLenStatus,
                'message' => __("filament-seo-pro::seo.checks.description_length.{$descLenStatus}"),
            ];
            $score += $descLenStatus === 'pass' ? 1 : ($descLenStatus === 'warn' ? 0.5 : 0);
            $totalChecks++;
        }

        // URL / Slug checks
        if (! empty($this->slug)) {
            $slugHasKeyword = ! empty($this->focusKeyword) &&
                str_contains(mb_strtolower($this->slug), mb_strtolower(str_replace(' ', '-', $this->focusKeyword)));

            $checks[] = [
                'key' => 'slug_keyword',
                'category' => 'url',
                'label' => __('filament-seo-pro::seo.checks.slug_keyword.label'),
                'status' => $slugHasKeyword ? 'pass' : 'fail',
                'message' => $slugHasKeyword
                    ? __('filament-seo-pro::seo.checks.slug_keyword.pass')
                    : __('filament-seo-pro::seo.checks.slug_keyword.fail'),
            ];
            $score += $slugHasKeyword ? 1 : 0;
            $totalChecks++;

            $slugLen = mb_strlen($this->slug);
            $slugLenStatus = $slugLen <= 75 ? 'pass' : ($slugLen <= 100 ? 'warn' : 'fail');
            $checks[] = [
                'key' => 'slug_length',
                'category' => 'url',
                'label' => __('filament-seo-pro::seo.checks.slug_length.label'),
                'status' => $slugLenStatus,
                'message' => __("filament-seo-pro::seo.checks.slug_length.{$slugLenStatus}"),
            ];
            $score += $slugLenStatus === 'pass' ? 1 : ($slugLenStatus === 'warn' ? 0.5 : 0);
            $totalChecks++;
        }

        // Content checks
        if (! empty($this->content)) {
            $plainContent = strip_tags($this->content);
            $wordCount = str_word_count($plainContent);

            $contentLenStatus = $wordCount >= 300 ? 'pass' : ($wordCount >= 150 ? 'warn' : 'fail');
            $checks[] = [
                'key' => 'content_length',
                'category' => 'content',
                'label' => __('filament-seo-pro::seo.checks.content_length.label'),
                'status' => $contentLenStatus,
                'message' => __("filament-seo-pro::seo.checks.content_length.{$contentLenStatus}"),
            ];
            $score += $contentLenStatus === 'pass' ? 1 : ($contentLenStatus === 'warn' ? 0.5 : 0);
            $totalChecks++;

            if (! empty($this->focusKeyword)) {
                $keywordCount = mb_substr_count(mb_strtolower($plainContent), mb_strtolower($this->focusKeyword));
                $density = $wordCount > 0 ? ($keywordCount / $wordCount) * 100 : 0;
                $densityStatus = ($density >= 1 && $density <= 3) ? 'pass' : (($density > 0 && $density < 5) ? 'warn' : 'fail');

                $checks[] = [
                    'key' => 'keyword_density',
                    'category' => 'content',
                    'label' => __('filament-seo-pro::seo.checks.keyword_density.label'),
                    'status' => $densityStatus,
                    'message' => __("filament-seo-pro::seo.checks.keyword_density.{$densityStatus}"),
                ];
                $score += $densityStatus === 'pass' ? 1 : ($densityStatus === 'warn' ? 0.5 : 0);
                $totalChecks++;

                // Check first paragraph for keyword
                $firstParagraph = mb_strtolower(mb_substr($plainContent, 0, 200));
                $introHasKeyword = str_contains($firstParagraph, mb_strtolower($this->focusKeyword));
                $checks[] = [
                    'key' => 'keyword_in_intro',
                    'category' => 'content',
                    'label' => __('filament-seo-pro::seo.checks.keyword_in_intro.label'),
                    'status' => $introHasKeyword ? 'pass' : 'fail',
                    'message' => $introHasKeyword
                        ? __('filament-seo-pro::seo.checks.keyword_in_intro.pass')
                        : __('filament-seo-pro::seo.checks.keyword_in_intro.fail'),
                ];
                $score += $introHasKeyword ? 1 : 0;
                $totalChecks++;
            }
        }

        $finalScore = $totalChecks > 0 ? (int) round(($score / $totalChecks) * 100) : 0;

        return [
            'score' => $finalScore,
            'checks' => $checks,
        ];
    }

    /**
     * @return View
     */
    public function render()
    {
        return view('filament-seo-pro::livewire.seo-analyzer-panel');
    }
}
