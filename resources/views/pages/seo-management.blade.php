<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Stats Overview --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
            {{-- Average SEO Score --}}
            <x-filament::section>
                <div class="flex items-center gap-x-3">
                    <div class="seo-pro-stat-icon seo-pro-stat-icon--{{ $this->getColorForScore($this->getAverageScore()) }}">
                        <x-heroicon-o-chart-bar class="h-6 w-6" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('filament-seo-pro::seo.average_score') }}
                        </p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $this->getAverageScore() }}
                            <span class="text-sm font-normal text-gray-500">/100</span>
                        </p>
                    </div>
                </div>
            </x-filament::section>

            {{-- Missing Titles --}}
            <x-filament::section>
                <div class="flex items-center gap-x-3">
                    <div class="seo-pro-stat-icon seo-pro-stat-icon--warning">
                        <x-heroicon-o-document-minus class="h-6 w-6" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('filament-seo-pro::seo.missing_titles') }}
                        </p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $this->getMissingTitlesCount() }}
                        </p>
                    </div>
                </div>
            </x-filament::section>

            {{-- Missing Descriptions --}}
            <x-filament::section>
                <div class="flex items-center gap-x-3">
                    <div class="seo-pro-stat-icon seo-pro-stat-icon--warning">
                        <x-heroicon-o-document-text class="h-6 w-6" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('filament-seo-pro::seo.missing_descriptions') }}
                        </p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $this->getMissingDescriptionsCount() }}
                        </p>
                    </div>
                </div>
            </x-filament::section>

            {{-- Health Score --}}
            <x-filament::section>
                <div class="flex items-center gap-x-3">
                    <div class="seo-pro-stat-icon seo-pro-stat-icon--{{ $this->getHealthyPercentage() >= 60 ? 'success' : 'warning' }}">
                        <x-heroicon-o-heart class="h-6 w-6" />
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('filament-seo-pro::seo.health_score') }}
                        </p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $this->getHealthyPercentage() }}%
                        </p>
                    </div>
                </div>
            </x-filament::section>
        </div>

        {{-- Lowest Scoring Content --}}
        @if($this->getLowestScoring()->count() > 0)
        <x-filament::section>
            <x-slot name="heading">
                {{ __('filament-seo-pro::seo.lowest_scoring') }}
            </x-slot>
            <x-slot name="description">
                {{ __('filament-seo-pro::seo.lowest_scoring_description') }}
            </x-slot>

            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($this->getLowestScoring() as $meta)
                <div class="flex items-center justify-between py-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            {{ $meta->title ?? __('filament-seo-pro::seo.untitled') }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ class_basename($meta->seoable_type) }}
                        </p>
                    </div>
                    <div>
                        <x-filament::badge :color="$this->getColorForScore($meta->seo_score)">
                            {{ $meta->seo_score }}/100
                        </x-filament::badge>
                    </div>
                </div>
                @endforeach
            </div>
        </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
