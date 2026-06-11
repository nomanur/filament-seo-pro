@php
    $ogTitle = $get('seo.og_title') ?? $get('og_title') ?? $get('seo.title') ?? $get('title') ?? '';
    $ogDescription = $get('seo.og_description') ?? $get('og_description') ?? $get('seo.meta_description') ?? $get('meta_description') ?? '';
    $ogImage = $get('seo.og_image') ?? $get('og_image') ?? '';
    $siteUrl = config('app.url', 'https://example.com');
    $domain = parse_url($siteUrl, PHP_URL_HOST) ?? $siteUrl;
@endphp

<div
    wire:key="og-preview-{{ Str::random(8) }}"
    x-data="{
        title: @js($ogTitle),
        description: @js($ogDescription),
        image: @js($ogImage),
        domain: @js($domain),
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

        get displayTitle() {
            return this.getString(this.title) || '{{ __("filament-seo-pro::seo.preview.untitled") }}';
        },

        get displayDescription() {
            let d = this.getString(this.description);
            return d
                ? (d.length > 160 ? d.substring(0, 160) + '...' : d)
                : '{{ __("filament-seo-pro::seo.preview.no_description") }}';
        },

        get hasImage() {
            let img = this.getString(this.image);
            return img && img.length > 0;
        }
    }"
    x-on:seo-field-updated.window="
        if ($event.detail.field === 'og_title') title = $event.detail.value;
        if ($event.detail.field === 'og_description') description = $event.detail.value;
        if ($event.detail.field === 'og_image') image = $event.detail.value;
    "
    class="seo-pro-og-preview"
>
    {{-- Image --}}
    <div class="seo-pro-og-preview__image-container">
        <template x-if="hasImage">
            <img :src="getString(image)" alt="" class="seo-pro-og-preview__image" />
        </template>
        <template x-if="!hasImage">
            <div class="seo-pro-og-preview__placeholder">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="seo-pro-og-preview__placeholder-icon">
                    <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z" clip-rule="evenodd" />
                </svg>
            </div>
        </template>
    </div>

    {{-- Content --}}
    <div class="seo-pro-og-preview__content">
        <span class="seo-pro-og-preview__domain" x-text="domain.toUpperCase()"></span>
        <h4 class="seo-pro-og-preview__title" x-text="displayTitle"></h4>
        <p class="seo-pro-og-preview__description" x-text="displayDescription"></p>
    </div>
</div>
