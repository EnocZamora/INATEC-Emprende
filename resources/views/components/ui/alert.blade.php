@props(['variant' => 'error', 'title' => null])

@php
    $config = [
        'error' => [
            'icon' => 'alert-circle',
            'surface' => 'border-red-200 bg-red-50/60',
            'iconTone' => 'text-red-500',
            'titleTone' => 'text-red-900',
            'bodyTone' => 'text-red-800',
        ],
        'success' => [
            'icon' => 'check-circle',
            'surface' => 'border-emerald-200 bg-emerald-50/60',
            'iconTone' => 'text-emerald-600',
            'titleTone' => 'text-emerald-900',
            'bodyTone' => 'text-emerald-800',
        ],
        'info' => [
            'icon' => 'information-circle',
            'surface' => 'border-blue-200 bg-blue-50/60',
            'iconTone' => 'text-blue-600',
            'titleTone' => 'text-blue-900',
            'bodyTone' => 'text-blue-800',
        ],
        'warning' => [
            'icon' => 'exclamation-triangle',
            'surface' => 'border-amber-200 bg-amber-50/60',
            'iconTone' => 'text-amber-600',
            'titleTone' => 'text-amber-900',
            'bodyTone' => 'text-amber-800',
        ],
    ][$variant];
@endphp

<div
    role="{{ $variant === 'error' ? 'alert' : 'status' }}"
    {{ $attributes->merge(['class' => 'flex gap-2.5 rounded-lg border px-3.5 py-3 '.$config['surface']]) }}
>
    <x-ui.icon :name="$config['icon']" class="mt-px h-4 w-4 shrink-0 {{ $config['iconTone'] }}" />

    <div class="min-w-0 text-[0.8125rem] leading-5">
        @if ($title)
            <p class="font-semibold {{ $config['titleTone'] }}">{{ $title }}</p>
            <div class="mt-0.5 {{ $config['bodyTone'] }}">{{ $slot }}</div>
        @else
            <div class="{{ $config['bodyTone'] }}">{{ $slot }}</div>
        @endif
    </div>
</div>
