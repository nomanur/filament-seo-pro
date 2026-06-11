<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-magnifying-glass-circle class="h-5 w-5 text-primary-500" />
                {{ __('filament-seo-pro::seo.seo_overview') }}
            </div>
        </x-slot>

        <div class="space-y-4">
            {{-- Stats Row --}}
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                {{-- Average Score --}}
                <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-800">
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        {{ __('filament-seo-pro::seo.avg_score') }}
                    </p>
                    <p class="mt-1 text-xl font-bold" style="color: var(--seo-pro-score-color, currentColor)">
                        {{ $this->getAverageScore() }}
                    </p>
                </div>

                {{-- Missing Titles --}}
                <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-800">
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        {{ __('filament-seo-pro::seo.missing_titles') }}
                    </p>
                    <p class="mt-1 text-xl font-bold text-amber-600 dark:text-amber-400">
                        {{ $this->getMissingTitlesCount() }}
                    </p>
                </div>

                {{-- Missing Descriptions --}}
                <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-800">
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        {{ __('filament-seo-pro::seo.missing_descriptions') }}
                    </p>
                    <p class="mt-1 text-xl font-bold text-amber-600 dark:text-amber-400">
                        {{ $this->getMissingDescriptionsCount() }}
                    </p>
                </div>

                {{-- Total Content --}}
                <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-800">
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        {{ __('filament-seo-pro::seo.total_content') }}
                    </p>
                    <p class="mt-1 text-xl font-bold text-gray-900 dark:text-white">
                        {{ $this->getTotalContentCount() }}
                    </p>
                </div>
            </div>

            {{-- Lowest Scoring Items --}}
            @if($this->getLowestScoring(3)->count() > 0)
            <div>
                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('filament-seo-pro::seo.needs_attention') }}
                </h4>
                <div class="space-y-2">
                    @foreach($this->getLowestScoring(3) as $meta)
                    <div class="flex items-center justify-between rounded-lg border border-gray-200 px-3 py-2 dark:border-gray-700">
                        <span class="text-sm text-gray-700 dark:text-gray-300 truncate max-w-[200px]">
                            {{ $meta->title ?? __('filament-seo-pro::seo.untitled') }}
                        </span>
                        <x-filament::badge size="sm" :color="$this->getColorForScore($meta->seo_score)">
                            {{ $meta->seo_score }}
                        </x-filament::badge>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
