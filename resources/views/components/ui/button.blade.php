@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'icon' => null,
    'trailing' => null,
    'iconOnly' => false,
    'type' => 'button',
])

@php
    $variants = [
        'primary' => 'border border-transparent bg-blue-700 text-white shadow-[0_1px_1px_rgb(15_23_42/0.08)] hover:bg-blue-800',
        'secondary' => 'border border-slate-300/90 bg-white text-slate-700 shadow-[0_1px_1px_rgb(15_23_42/0.03)] hover:border-slate-400 hover:text-slate-900',
        'ghost' => 'border border-transparent text-slate-600 hover:bg-slate-100 hover:text-slate-900',
        'subtle' => 'border border-transparent bg-blue-50 text-blue-800 hover:bg-blue-100',
        'white' => 'border border-transparent bg-white text-blue-800 shadow-[0_1px_2px_rgb(2_6_23/0.16)] hover:bg-blue-50',
        'danger' => 'border border-transparent bg-red-600 text-white shadow-[0_1px_1px_rgb(15_23_42/0.08)] hover:bg-red-700',
        'danger-outline' => 'border border-red-200 bg-white text-red-700 hover:border-red-300 hover:bg-red-50',
    ];

    $sizes = [
        'sm' => $iconOnly ? 'h-9 w-9' : 'h-9 px-3 text-[0.8125rem]',
        'md' => $iconOnly ? 'h-10 w-10' : 'h-10 px-4 text-[0.875rem]',
        'lg' => $iconOnly ? 'h-11 w-11' : 'h-11 px-5 text-[0.9375rem]',
    ];

    $iconSize = match ($size) {
        'sm' => 'h-3.5 w-3.5',
        'lg' => 'h-[1.125rem] w-[1.125rem]',
        default => 'h-4 w-4',
    };

    $classes = 'inline-flex shrink-0 items-center justify-center gap-1.5 rounded-lg font-medium outline-none transition-[background-color,border-color,color,box-shadow,transform] duration-150 ease-out focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-60 motion-safe:active:scale-[0.98] '.$variants[$variant].' '.$sizes[$size];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)
            <x-ui.icon :name="$icon" :class="$iconSize.' shrink-0 opacity-90'" />
        @endif

        @if (! $iconOnly)
            {{ $slot }}
        @endif

        @if ($trailing)
            <x-ui.icon :name="$trailing" :class="$iconSize.' shrink-0 opacity-80'" />
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)
            <x-ui.icon :name="$icon" :class="$iconSize.' shrink-0 opacity-90'" />
        @endif

        @if (! $iconOnly)
            {{ $slot }}
        @endif

        @if ($trailing)
            <x-ui.icon :name="$trailing" :class="$iconSize.' shrink-0 opacity-80'" />
        @endif
    </button>
@endif
