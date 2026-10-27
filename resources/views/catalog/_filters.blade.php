@php
    $selectedDepartment = (int) ($filters['department_id'] ?? 0);
    $selectedMunicipality = (int) ($filters['municipality_id'] ?? 0);
    $selectedCenter = (int) ($filters['center_id'] ?? 0);
    $selectedCareer = (int) ($filters['career_id'] ?? 0);

    $activeCount = collect($filters)
        ->filter(fn ($value): bool => $value !== null && $value !== '' && $value !== 0 && $value !== '0')
        ->count();

    // When a department is chosen, the center list narrows to its municipalities.
    $visibleCenters = $selectedDepartment > 0
        ? $centers->whereIn('municipality_id', $municipalities->pluck('id'))
        : $centers;

    // Keep a deep-linked center visible even if it sits outside the chosen department.
    $selectedCenterModel = $centers->firstWhere('id', $selectedCenter);

    if ($selectedCenterModel && ! $visibleCenters->contains('id', $selectedCenter)) {
        $visibleCenters = $visibleCenters->push($selectedCenterModel)->sortBy('name')->values();
    }
@endphp

<div id="filtros" class="overflow-hidden rounded-xl border border-slate-200/80 bg-white">
    <div class="flex items-center justify-between gap-3 border-b border-slate-200/80 px-4 py-3">
        <p class="flex items-center gap-1.5 text-[0.8125rem] font-semibold text-slate-900">
            <x-ui.icon name="adjustments" class="h-4 w-4 text-slate-400" />
            Filtros

            @if ($activeCount > 0)
                <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-blue-50 px-1.5 text-[0.625rem] font-semibold tabular-nums text-blue-800">
                    {{ $activeCount }}
                </span>
            @endif
        </p>

        <div class="flex items-center gap-1">
            @if ($activeCount > 0)
                <a href="{{ route('catalog.index') }}" class="rounded-md px-1.5 py-1 text-[0.6875rem] font-medium text-slate-500 underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline">
                    Limpiar
                </a>
            @endif

            <button
                type="button"
                @click="filtersOpen = ! filtersOpen"
                :aria-expanded="filtersOpen"
                aria-controls="filtros-panel"
                class="-me-1 inline-flex h-7 w-7 items-center justify-center rounded-md text-slate-500 transition-colors duration-150 hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600/40 lg:hidden"
            >
                <x-ui.icon name="chevron-down" class="h-4 w-4 transition-transform duration-150" x-bind:class="filtersOpen ? 'rotate-180' : ''" />
                <span class="sr-only">Mostrar u ocultar filtros</span>
            </button>
        </div>
    </div>

    <div id="filtros-panel" class="space-y-4 p-4" :class="filtersOpen ? 'is-open' : ''">
        <x-ui.filter-select name="department_id" label="Departamento" :resets="['municipality_id', 'center_id', 'career_id']">
            <option value="">Todos los departamentos</option>
            @foreach ($departments as $department)
                <option value="{{ $department->id }}" @selected($selectedDepartment === $department->id)>
                    {{ $department->name }}
                </option>
            @endforeach
        </x-ui.filter-select>

        @if ($municipalities->isNotEmpty())
            <x-ui.filter-select name="municipality_id" label="Municipio" :resets="['center_id', 'career_id']">
                <option value="">Todos los municipios</option>
                @foreach ($municipalities as $municipality)
                    <option value="{{ $municipality->id }}" @selected($selectedMunicipality === $municipality->id)>
                        {{ $municipality->name }}
                    </option>
                @endforeach
            </x-ui.filter-select>
        @else
            <x-ui.filter-select name="municipality_id" label="Municipio" disabled>
                <option value="">Elige un departamento</option>
            </x-ui.filter-select>
        @endif

        <x-ui.filter-select name="center_id" label="Centro tecnológico" :resets="['career_id']">
            <option value="">Todos los centros</option>
            @foreach ($visibleCenters as $center)
                <option value="{{ $center->id }}" @selected($selectedCenter === $center->id)>
                    {{ $center->name }}
                </option>
            @endforeach
        </x-ui.filter-select>

        @if ($careers->isNotEmpty())
            <x-ui.filter-select name="career_id" label="Carrera">
                <option value="">Todas las carreras</option>
                @foreach ($careers as $career)
                    <option value="{{ $career->id }}" @selected($selectedCareer === $career->id)>
                        {{ $career->name }}
                    </option>
                @endforeach
            </x-ui.filter-select>
        @else
            <x-ui.filter-select name="career_id" label="Carrera" disabled>
                <option value="">Elige un centro</option>
            </x-ui.filter-select>
        @endif

        <p class="flex items-start gap-1.5 border-t border-slate-100 pt-3.5 text-[0.6875rem] leading-5 text-slate-500">
            <x-ui.icon name="information-circle" class="mt-px h-3.5 w-3.5 shrink-0 text-slate-400" />
            Los filtros se aplican automáticamente. Puedes combinarlos con la búsqueda.
        </p>
    </div>
</div>
