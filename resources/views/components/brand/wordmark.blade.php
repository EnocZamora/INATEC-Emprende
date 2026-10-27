@props(['compact' => false])

{{--
    Typographic wordmark. The source artwork ships as a neon gradient on a black
    JPEG, which fights a light institutional surface; the bulb is redrawn in the
    portal's single blue so the mark still carries the original idea.
--}}
<a
    href="{{ route('home') }}"
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5 rounded-md outline-none focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2']) }}
    aria-label="INATEC Emprende — ir al catálogo"
>
    <svg class="h-[1.375rem] w-[1.375rem] shrink-0 text-blue-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3a6 6 0 0 0-3.6 10.8c.5.4.85 1 .85 1.65V16h5.5v-.55c0-.65.35-1.25.85-1.65A6 6 0 0 0 12 3Z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 19h4.5M10.5 21h3" />
    </svg>

    <span class="flex flex-col">
        <span class="text-[0.9375rem] leading-none font-semibold tracking-[-0.015em] whitespace-nowrap text-slate-900">
            INATEC <span class="font-medium text-blue-700">Emprende</span>
        </span>
        @unless ($compact)
            <span class="mt-1 text-[0.625rem] leading-none font-medium tracking-[0.12em] whitespace-nowrap text-slate-400 uppercase">
                Portal de emprendimientos
            </span>
        @endunless
    </span>
</a>
