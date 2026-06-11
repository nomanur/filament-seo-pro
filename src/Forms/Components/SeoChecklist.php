<?php

declare(strict_types=1);

namespace Nomanur\FilamentSeoPro\Forms\Components;

use Filament\Schemas\Components\Component;
use Nomanur\FilamentSeoPro\Services\SeoAnalyzer;

/**
 * SEO Checklist component.
 *
 * Displays a list of SEO checks with pass/warn/fail status indicators.
 * Updates live as form data changes.
 */
class SeoChecklist extends Component
{
    protected string $view = 'filament-seo-pro::components.seo-checklist';

    public static function make(string $name = 'seo_checklist'): static
    {
        $static = app(static::class);

        $static->statePath($name);

        return $static;
    }

    /**
     * Get checklist items from the current form state.
     *
     * @return array<int, array{key: string, label: string, status: string, message: string, category: string}>
     */
    public function getChecks(callable $get): array
    {
        $analyzer = app(SeoAnalyzer::class);

        $data = [
            'title' => $this->normalizeState($get('seo.title') ?? $get('title') ?? ''),
            'description' => $this->normalizeState($get('seo.description') ?? ''),
            'focus_keyword' => $this->normalizeState($get('seo.focus_keyword') ?? ''),
            'content' => $this->normalizeState($get('content') ?? ''),
            'slug' => $this->normalizeState($get('slug') ?? ''),
            'url' => '',
        ];

        $result = $analyzer->analyze($data);

        return array_map(fn ($check) => [
            'key' => $check->key,
            'label' => $check->label,
            'status' => $check->status->value,
            'message' => $check->message,
            'category' => $check->category,
        ], $result->checks);
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
