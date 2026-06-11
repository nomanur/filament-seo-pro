<div class="seo-pro-analyzer-panel">

    {{-- Loading Overlay --}}
    <div wire:loading wire:target="analyzeContent" class="seo-pro-analyzer-panel__loading">
        <svg class="seo-pro-analyzer-panel__spinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span>{{ __('filament-seo-pro::seo.analyzer.analyzing') }}</span>
    </div>

    <div wire:loading.remove wire:target="analyzeContent">
        @if (empty($analysisResult) && empty($title) && empty($content))
            {{-- Empty State --}}
            <div class="seo-pro-checklist__empty" style="padding: 3rem 1rem;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="seo-pro-checklist__empty-icon" style="width: 2.5rem; height: 2.5rem;">
                    <path fill-rule="evenodd" d="M10.5 3.75a6.75 6.75 0 100 13.5 6.75 6.75 0 000-13.5zM2.25 10.5a8.25 8.25 0 1114.59 5.28l4.69 4.69a.75.75 0 11-1.06 1.06l-4.69-4.69A8.25 8.25 0 012.25 10.5z" clip-rule="evenodd" />
                </svg>
                <p>{{ __('filament-seo-pro::seo.analyzer.no_data') }}</p>
            </div>
        @else
            {{-- Google Preview Section --}}
            <div class="seo-pro-analyzer-panel__section">
                <h3 class="seo-pro-analyzer-panel__heading">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="seo-pro-analyzer-panel__heading-icon">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                    </svg>
                    {{ __('filament-seo-pro::seo.preview.google_heading') }}
                </h3>

                <div
                    x-data="{
                        title: @js($title),
                        description: @js($description ?? ''),
                        slug: @js($slug ?? ''),
                        siteUrl: @js(config('app.url', 'https://example.com')),

                        get truncatedTitle() {
                            if (!this.title) return '{{ __("filament-seo-pro::seo.preview.untitled") }}';
                            return this.title.length > 60 ? this.title.substring(0, 60) + '...' : this.title;
                        },

                        get truncatedDescription() {
                            if (!this.description) return '{{ __("filament-seo-pro::seo.preview.no_description") }}';
                            return this.description.length > 160 ? this.description.substring(0, 160) + '...' : this.description;
                        },

                        get formattedUrl() {
                            let base = this.siteUrl.replace(/^https?:\/\//, '').replace(/\/$/, '');
                            let path = this.slug ? ' › ' + this.slug.replace(/\//g, ' › ') : '';
                            return base + path;
                        }
                    }"
                    class="seo-pro-google-preview"
                >
                    <div class="seo-pro-google-preview__site-row">
                        <div class="seo-pro-google-preview__favicon">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="8" cy="8" r="7" fill="#f1f3f4" stroke="#dadce0" stroke-width="1"/>
                                <text x="8" y="11" text-anchor="middle" font-size="9" fill="#5f6368" font-family="Arial">S</text>
                            </svg>
                        </div>
                        <div class="seo-pro-google-preview__site-info">
                            <div class="seo-pro-google-preview__site-name" x-text="siteUrl.replace(/^https?:\/\//, '').replace(/\/$/, '')"></div>
                            <div class="seo-pro-google-preview__url" x-text="formattedUrl"></div>
                        </div>
                    </div>
                    <h3 class="seo-pro-google-preview__title">
                        <a href="javascript:void(0)" x-text="truncatedTitle"></a>
                    </h3>
                    <p class="seo-pro-google-preview__description" x-text="truncatedDescription"></p>
                </div>
            </div>

            <hr class="seo-pro-divider">

            {{-- SEO Score Section --}}
            @if (!empty($analysisResult))
                <div class="seo-pro-analyzer-panel__section">
                    <h3 class="seo-pro-analyzer-panel__heading">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="seo-pro-analyzer-panel__heading-icon">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-11.25a.75.75 0 00-1.5 0v2.5h-2.5a.75.75 0 000 1.5h2.5v2.5a.75.75 0 001.5 0v-2.5h2.5a.75.75 0 000-1.5h-2.5v-2.5z" clip-rule="evenodd" />
                        </svg>
                        {{ __('filament-seo-pro::seo.sections.score') }}
                    </h3>

                    @php
                        $score = $analysisResult['score'] ?? 0;
                    @endphp

                    <div
                        x-data="{
                            score: @js($score),
                            radius: 54,
                            stroke: 8,

                            get circumference() { return 2 * Math.PI * this.radius; },
                            get offset() { return this.circumference - (this.score / 100) * this.circumference; },
                            get color() {
                                if (this.score <= 30) return 'var(--seo-pro-color-red, #ef4444)';
                                if (this.score <= 60) return 'var(--seo-pro-color-orange, #f59e0b)';
                                if (this.score <= 80) return 'var(--seo-pro-color-blue, #3b82f6)';
                                return 'var(--seo-pro-color-green, #22c55e)';
                            },
                            get grade() {
                                if (this.score <= 30) return '{{ __("filament-seo-pro::seo.grade.poor") }}';
                                if (this.score <= 60) return '{{ __("filament-seo-pro::seo.grade.needs_work") }}';
                                if (this.score <= 80) return '{{ __("filament-seo-pro::seo.grade.good") }}';
                                return '{{ __("filament-seo-pro::seo.grade.excellent") }}';
                            },
                            get bgColor() {
                                if (this.score <= 30) return 'rgba(239, 68, 68, 0.08)';
                                if (this.score <= 60) return 'rgba(245, 158, 11, 0.08)';
                                if (this.score <= 80) return 'rgba(59, 130, 246, 0.08)';
                                return 'rgba(34, 197, 94, 0.08)';
                            }
                        }"
                        class="seo-pro-score-gauge"
                    >
                        <div class="seo-pro-score-gauge__chart">
                            <svg width="140" height="140" viewBox="0 0 120 120" class="seo-pro-score-gauge__svg">
                                <circle cx="60" cy="60" :r="radius" fill="none" stroke="var(--seo-pro-color-track, #e5e7eb)" :stroke-width="stroke" class="seo-pro-score-gauge__track" />
                                <circle cx="60" cy="60" :r="radius" fill="none" :stroke="color" :stroke-width="stroke" stroke-linecap="round" :stroke-dasharray="circumference" :stroke-dashoffset="offset" transform="rotate(-90 60 60)" class="seo-pro-score-gauge__fill" />
                            </svg>
                            <div class="seo-pro-score-gauge__value">
                                <span class="seo-pro-score-gauge__number" x-text="score" :style="'color:' + color"></span>
                                <span class="seo-pro-score-gauge__max">/100</span>
                            </div>
                        </div>
                        <div class="seo-pro-score-gauge__label">
                            <span class="seo-pro-score-gauge__grade" x-text="grade" :style="'color:' + color + '; background-color:' + bgColor"></span>
                        </div>
                    </div>
                </div>

                <hr class="seo-pro-divider">

                {{-- SEO Checklist Section --}}
                @if (!empty($analysisResult['checks']))
                    <div class="seo-pro-analyzer-panel__section">
                        <h3 class="seo-pro-analyzer-panel__heading">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="seo-pro-analyzer-panel__heading-icon">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                            </svg>
                            {{ __('filament-seo-pro::seo.sections.checklist') }}
                        </h3>

                        @php
                            $checks = $analysisResult['checks'] ?? [];
                            $categories = [
                                'title' => __('filament-seo-pro::seo.category.title'),
                                'description' => __('filament-seo-pro::seo.category.description'),
                                'url' => __('filament-seo-pro::seo.category.url'),
                                'content' => __('filament-seo-pro::seo.category.content'),
                                'links' => __('filament-seo-pro::seo.category.links'),
                            ];
                            $grouped = collect($checks)->groupBy('category');
                        @endphp

                        <div class="seo-pro-checklist">
                            @foreach ($grouped as $category => $items)
                                <div class="seo-pro-checklist__group">
                                    <h4 class="seo-pro-checklist__category">
                                        {{ $categories[$category] ?? Str::title($category) }}
                                    </h4>
                                    <ul class="seo-pro-checklist__items">
                                        @foreach ($items as $check)
                                            @php
                                                $status = $check['status'] ?? 'fail';
                                            @endphp
                                            <li class="seo-pro-checklist__item seo-pro-checklist__item--{{ $status }}">
                                                <div class="seo-pro-checklist__icon">
                                                    @if ($status === 'pass')
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="seo-pro-checklist__svg seo-pro-checklist__svg--pass">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                                        </svg>
                                                    @elseif ($status === 'warn')
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="seo-pro-checklist__svg seo-pro-checklist__svg--warn">
                                                            <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                                        </svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="seo-pro-checklist__svg seo-pro-checklist__svg--fail">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div class="seo-pro-checklist__content">
                                                    <span class="seo-pro-checklist__label">{{ $check['label'] ?? '' }}</span>
                                                    <span class="seo-pro-checklist__message">{{ $check['message'] ?? '' }}</span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        @endif
    </div>
</div>
