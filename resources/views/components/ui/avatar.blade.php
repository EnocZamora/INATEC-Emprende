@props(['name' => '', 'size' => 'md', 'tone' => 'blue'])

@php
    $parts = preg_split('/\s+/u', trim((string) $name)) ?: [];
    $initials = collect($parts)
        ->filter()
        ->take(2)
        ->map(fn (string $part): string => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
    $initials = $initials !== '' ? $initials : 'IN';

    $sizes = [
        'xs' => 'h-6 w-6 text-[0.5625rem]',
        'sm' => 'h-8 w-8 text-[0.625rem]',
        'md' => 'h-10 w-10 text-[0.75rem]',
        'lg' => 'h-12 w-12 text-[0.875rem]',
    ];

    $tones = [
        'blue' => 'border-blue-100 bg-blue-50 text-blue-800',
        'slate' => 'border-slate-200 bg-slate-100 text-slate-700',
        'white' => 'border-white/60 bg-white/15 text-white',
    ];
@endphp

<span
    {{ $attributes->merge([
        'class' => 'inline-flex shrink-0 items-center justify-center rounded-full border font-semibold tracking-[0.02em] select-none '.$sizes[$size].' '.$tones[$tone],
    ]) }}
    aria-hidden="true"
>{{ $initials }}</span>
