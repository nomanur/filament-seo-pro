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
        locale: @js(app()->getLocale()),

        getString(value) {
            if (typeof value === 'string') return value;
            if (value && typeof value === 'object') {
                if (Array.isArray(value)) {
                    return value.length > 0 ? this.getString(value[0]) : '';
                }
                if (value[this.locale] !== undefined && value[this.locale] !== null) {
                    return this.getString(value[this.locale]);
                }
                let values = Object.values(value);
                return values.length > 0 ? this.getString(values[0]) : '';
            }
            return '';
        },

        get truncatedTitle() {
            let t = this.getString(this.title);
            if (!t) return '{{ __("filament-seo-pro::seo.preview.untitled") }}';
            return t.length > 60 ? t.substring(0, 60) + '...' : t;
        },

        get truncatedDescription() {
            let d = this.getString(this.description);
            if (!d) return '{{ __("filament-seo-pro::seo.preview.no_description") }}';
            return d.length > 160 ? d.substring(0, 160) + '...' : d;
        },

        get formattedUrl() {
            let s = this.getString(this.slug);
            let base = this.siteUrl.replace(/^https?:\/\//, '').replace(/\/$/, '');
            let path = s ? ' › ' + s.replace(/\//g, ' › ') : '';
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
