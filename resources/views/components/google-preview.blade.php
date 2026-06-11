@php
    $state = $getState();
    $recordTitle = $get('seo.title') ?? $get('title') ?? '';
    $recordDescription = $get('seo.meta_description') ?? $get('meta_description') ?? '';
    $recordSlug = $get('seo.slug') ?? $get('slug') ?? '';
    $siteUrl = config('app.url', 'https://example.com');
@endphp

<div
    wire:key="google-preview-{{ Str::random(8) }}"
    x-data="{
        title: @js($recordTitle),
        description: @js($recordDescription),
        slug: @js($recordSlug),
        siteUrl: @js($siteUrl),

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
    x-on:seo-field-updated.window="
        if ($event.detail.field === 'title') title = $event.detail.value;
        if ($event.detail.field === 'description') description = $event.detail.value;
        if ($event.detail.field === 'slug') slug = $event.detail.value;
    "
    class="seo-pro-google-preview"
>
    {{-- Google Favicon + Site Name --}}
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

    {{-- Title --}}
    <h3 class="seo-pro-google-preview__title">
        <a href="javascript:void(0)" x-text="truncatedTitle"></a>
    </h3>

    {{-- Description --}}
    <p class="seo-pro-google-preview__description" x-text="truncatedDescription"></p>
</div>
