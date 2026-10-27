@props([
    'projects',
    'departments',
    'municipalities',
    'centers',
    'careers',
    'categories',
    'filters',
    'action' => null,
])

@php
    $activeCount = collect($filters)
        ->filter(fn ($value): bool => $value !== null && $value !== '' && $value !== 0 && $value !== '0')
        ->count();
@endphp

<form
    method="GET"
    action="{{ $action ?? route('catalog.index') }}"
    x-data="{ filtersOpen: {{ $activeCount > 0 ? 'true' : 'false' }} }"
    class="scroll-mt-24"
    id="buscador"
>
    @include('catalog._search')

    <div class="mt-6 lg:grid lg:grid-cols-[16.5rem_1fr] lg:items-start lg:gap-10">
        <aside class="mb-6 lg:sticky lg:top-20 lg:mb-0 lg:self-start" aria-label="Filtros del catálogo">
            @include('catalog._filters')
        </aside>

        <div class="min-w-0">
            @include('catalog._results')
        </div>
    </div>
</form>
