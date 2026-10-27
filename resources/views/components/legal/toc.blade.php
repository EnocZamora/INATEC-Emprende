@props(['items', 'otherLabel', 'otherUrl'])

<aside {{ $attributes->merge(['class' => 'lg:sticky lg:top-20 lg:self-start']) }}>
    <details class="group rounded-xl border border-slate-200/80 bg-white lg:hidden">
        <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-3 text-[0.8125rem] font-medium text-slate-700 outline-none focus-visible:ring-2 focus-visible:ring-blue-600/40 [&::-webkit-details-marker]:hidden">
            En esta página
            <x-ui.icon name="chevron-down" class="h-4 w-4 text-slate-400 transition-transform duration-150 group-open:rotate-180" />
        </summary>

        <nav class="border-t border-slate-200/80 p-2" aria-label="Contenido de la página">
            @foreach ($items as $anchor => $label)
                <a href="#{{ $anchor }}" class="block rounded-md px-2.5 py-2 text-[0.75rem] leading-5 text-slate-600 transition-colors duration-150 hover:bg-slate-50 hover:text-slate-900">
                    {{ $label }}
                </a>
            @endforeach
        </nav>
    </details>

    <div class="hidden lg:block">
        <p class="text-[0.625rem] font-semibold tracking-[0.14em] text-slate-400 uppercase">En esta página</p>

        <nav class="mt-3 space-y-0.5" aria-label="Contenido de la página">
            @foreach ($items as $anchor => $label)
                <a href="#{{ $anchor }}" class="block rounded-md px-2.5 py-1.5 text-[0.75rem] leading-5 text-slate-500 transition-colors duration-150 hover:bg-slate-50 hover:text-slate-900">
                    {{ $label }}
                </a>
            @endforeach
        </nav>

        <div class="mt-5 border-t border-slate-200/80 pt-4">
            <a href="{{ $otherUrl }}" class="group inline-flex items-center gap-1.5 text-[0.75rem] font-medium text-blue-700 underline-offset-4 transition-colors duration-150 hover:text-blue-800 hover:underline">
                {{ $otherLabel }}
                <x-ui.icon name="arrow-right" class="h-3.5 w-3.5 transition-transform duration-150 group-hover:translate-x-0.5 motion-reduce:transition-none motion-reduce:group-hover:translate-x-0" />
            </a>
        </div>
    </div>
</aside>
