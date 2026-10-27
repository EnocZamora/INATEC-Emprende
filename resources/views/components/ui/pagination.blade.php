@props(['paginator'])

@if ($paginator->hasPages())
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();

        // Keep the control compact: first, last, and the pages around the
        // current one. Ellipses appear only when there is a real gap.
        $pages = collect([1, $last])
            ->merge(range(max(1, $current - 1), min($last, $current + 1)))
            ->unique()
            ->sort()
            ->values();

        $link = 'inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-transparent px-2 text-[0.8125rem] font-medium tabular-nums text-slate-600 outline-none transition-[background-color,border-color,color] duration-150 ease-out hover:bg-slate-100 hover:text-slate-900 focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2';
    @endphp

    <nav class="flex flex-col items-center justify-between gap-4 border-t border-slate-200/80 pt-5 sm:flex-row" role="navigation" aria-label="Paginación de proyectos">
        <p class="text-[0.75rem] leading-5 text-slate-500">
            Mostrando
            <span class="font-medium tabular-nums text-slate-700">{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}</span>
            de
            <span class="font-medium tabular-nums text-slate-700">{{ $paginator->total() }}</span>
            {{ $paginator->total() === 1 ? 'proyecto' : 'proyectos' }}
        </p>

        <div class="flex items-center gap-0.5">
            @if ($paginator->onFirstPage())
                <span class="inline-flex h-9 w-9 cursor-not-allowed items-center justify-center rounded-lg text-slate-300" aria-hidden="true">
                    <x-ui.icon name="chevron-left" class="h-4 w-4" />
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Página anterior" class="{{ $link }}">
                    <x-ui.icon name="chevron-left" class="h-4 w-4" />
                </a>
            @endif

            @php $previous = null; @endphp

            @foreach ($pages as $page)
                @if ($previous !== null && $page - $previous > 1)
                    <span class="inline-flex h-9 w-6 items-center justify-center text-[0.75rem] text-slate-400" aria-hidden="true">…</span>
                @endif

                @if ($page === $current)
                    <span aria-current="page" class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg bg-blue-700 px-2 text-[0.8125rem] font-semibold tabular-nums text-white shadow-[0_1px_1px_rgb(15_23_42/0.08)]">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $paginator->url($page) }}" aria-label="Ir a la página {{ $page }}" class="{{ $link }}">
                        {{ $page }}
                    </a>
                @endif

                @php $previous = $page; @endphp
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Página siguiente" class="{{ $link }}">
                    <x-ui.icon name="chevron-right" class="h-4 w-4" />
                </a>
            @else
                <span class="inline-flex h-9 w-9 cursor-not-allowed items-center justify-center rounded-lg text-slate-300" aria-hidden="true">
                    <x-ui.icon name="chevron-right" class="h-4 w-4" />
                </span>
            @endif
        </div>
    </nav>
@endif
