@php
    $state = $getState();
    $score = is_array($state) ? ($state['score'] ?? 0) : (int) ($state ?? 0);
@endphp

<div
    wire:key="seo-score-{{ Str::random(8) }}"
    x-data="{
        score: @js($score),
        radius: 54,
        stroke: 8,

        get circumference() {
            return 2 * Math.PI * this.radius;
        },

        get offset() {
            return this.circumference - (this.score / 100) * this.circumference;
        },

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
    x-on:seo-score-updated.window="score = $event.detail.score"
    class="seo-pro-score-gauge"
>
    <div class="seo-pro-score-gauge__chart">
        <svg
            width="140"
            height="140"
            viewBox="0 0 120 120"
            class="seo-pro-score-gauge__svg"
        >
            {{-- Background circle --}}
            <circle
                cx="60"
                cy="60"
                :r="radius"
                fill="none"
                stroke="var(--seo-pro-color-track, #e5e7eb)"
                :stroke-width="stroke"
                class="seo-pro-score-gauge__track"
            />

            {{-- Foreground arc --}}
            <circle
                cx="60"
                cy="60"
                :r="radius"
                fill="none"
                :stroke="color"
                :stroke-width="stroke"
                stroke-linecap="round"
                :stroke-dasharray="circumference"
                :stroke-dashoffset="offset"
                transform="rotate(-90 60 60)"
                class="seo-pro-score-gauge__fill"
            />
        </svg>

        {{-- Score number overlay --}}
        <div class="seo-pro-score-gauge__value">
            <span class="seo-pro-score-gauge__number" x-text="score" :style="'color:' + color"></span>
            <span class="seo-pro-score-gauge__max">/100</span>
        </div>
    </div>

    {{-- Grade label --}}
    <div class="seo-pro-score-gauge__label">
        <span
            class="seo-pro-score-gauge__grade"
            x-text="grade"
            :style="'color:' + color + '; background-color:' + bgColor"
        ></span>
    </div>
</div>
