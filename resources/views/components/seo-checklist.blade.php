@php
    $state = $getState();
    $checks = is_array($state) ? $state : [];

    $categories = [
        'title' => __('filament-seo-pro::seo.category.title'),
        'description' => __('filament-seo-pro::seo.category.description'),
        'url' => __('filament-seo-pro::seo.category.url'),
        'content' => __('filament-seo-pro::seo.category.content'),
        'links' => __('filament-seo-pro::seo.category.links'),
    ];

    $grouped = collect($checks)->groupBy('category');
@endphp

<div
    wire:key="seo-checklist-{{ Str::random(8) }}"
    class="seo-pro-checklist"
>
    @forelse ($grouped as $category => $items)
        <div class="seo-pro-checklist__group">
            <h4 class="seo-pro-checklist__category">
                {{ $categories[$category] ?? Str::title($category) }}
            </h4>

            <ul class="seo-pro-checklist__items">
                @foreach ($items as $check)
                    @php
                        $status = $check['status'] ?? 'fail';
                        $statusClasses = match($status) {
                            'pass' => 'seo-pro-checklist__item--pass',
                            'warn' => 'seo-pro-checklist__item--warn',
                            default => 'seo-pro-checklist__item--fail',
                        };
                    @endphp

                    <li class="seo-pro-checklist__item {{ $statusClasses }}">
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
                            <span class="seo-pro-checklist__label">
                                {{ $check['label'] ?? '' }}
                            </span>
                            <span class="seo-pro-checklist__message">
                                {{ $check['message'] ?? '' }}
                            </span>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @empty
        <div class="seo-pro-checklist__empty">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="seo-pro-checklist__empty-icon">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
            </svg>
            <p>{{ __('filament-seo-pro::seo.checklist.empty') }}</p>
        </div>
    @endforelse
</div>
