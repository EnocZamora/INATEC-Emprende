@props(['variant' => 'neutral', 'dot' => false, 'icon' => null])

@php
    $variants = [
        'neutral' => 'border-slate-200 bg-slate-50 text-slate-600',
        'blue' => 'border-blue-200/80 bg-blue-50 text-blue-800',
        'green' => 'border-emerald-200/80 bg-emerald-50 text-emerald-800',
        'amber' => 'border-amber-200/80 bg-amber-50 text-amber-900',
        'red' => 'border-red-200/80 bg-red-50 text-red-800',
        'white' => 'border-slate-900/5 bg-white/95 text-slate-700 shadow-[0_1px_2px_rgb(2_6_23/0.08)]',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-[0.6875rem] leading-none font-medium tracking-[0.01em] '.$variants[$variant]]) }}>
    @if ($dot)
        <span class="h-1.5 w-1.5 rounded-full bg-current opacity-70" aria-hidden="true"></span>
    @elseif ($icon)
        <x-ui.icon :name="$icon" class="h-3 w-3 shrink-0" />
    @endif

    {{ $slot }}
</span>
