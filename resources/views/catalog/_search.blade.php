@php
    $search = $filters['search'] ?? '';
    $clearUrl = route('catalog.index', collect($filters)->except('search')->filter()->all());
@endphp

<div>
    <label for="search" class="sr-only">Buscar proyectos por título o descripción</label>

    <div class="relative">
        <span class="pointer-events-none absolute inset-y-0 start-0 flex w-11 items-center justify-center text-slate-400">
            <x-ui.icon name="search" class="h-[1.125rem] w-[1.125rem]" />
        </span>

        <input
            id="search"
            name="search"
            type="search"
            value="{{ $search }}"
            placeholder="Busca por título o descripción…"
            autocomplete="off"
            spellcheck="false"
            class="block h-12 w-full appearance-none rounded-xl border border-slate-300/90 bg-white ps-11 pe-32 text-base text-slate-900 shadow-[0_1px_1px_rgb(15_23_42/0.03)] outline-none transition-[border-color,box-shadow] duration-150 ease-out placeholder:text-slate-400 hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 sm:text-[0.9375rem] [&::-webkit-search-cancel-button]:appearance-none"
        >

        <div class="absolute inset-y-0 end-1.5 flex items-center gap-0.5">
            @if (filled($search))
                <a
                    href="{{ $clearUrl }}"
                    class="flex h-8 w-8 items-center justify-center rounded-md text-slate-400 transition-colors duration-150 hover:bg-slate-100 hover:text-slate-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600/40"
                    aria-label="Limpiar la búsqueda"
                >
                    <x-ui.icon name="x" class="h-4 w-4" />
                </a>
            @endif

            <button
                type="submit"
                class="inline-flex h-9 items-center justify-center rounded-lg bg-blue-700 px-4 text-[0.8125rem] font-medium text-white outline-none transition-[background-color,transform] duration-150 ease-out hover:bg-blue-800 focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 motion-safe:active:scale-[0.98]"
            >
                Buscar
            </button>
        </div>
    </div>

    <p class="mt-2 text-[0.6875rem] leading-5 text-slate-500">
        Busca por título o descripción. Los filtros de ubicación y especialidad se combinan con tu búsqueda.
    </p>
</div>
