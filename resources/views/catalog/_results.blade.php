@php
    $search = $filters['search'] ?? '';
    $selectedCategory = $filters['category'] ?? '';

    $current = collect($filters)->filter(
        fn ($value): bool => $value !== null && $value !== '' && $value !== 0 && $value !== '0',
    );

    $categoryBase = $current->except('category')->all();

    $removeFilter = fn (array $keys): string => route('catalog.index', $current->except($keys)->all());

    $departmentName = $departments->firstWhere('id', $filters['department_id'] ?? 0)?->name;
    $municipalityName = $municipalities->firstWhere('id', $filters['municipality_id'] ?? 0)?->name;
    $centerName = $centers->firstWhere('id', $filters['center_id'] ?? 0)?->name;
    $careerName = $careers->firstWhere('id', $filters['career_id'] ?? 0)?->name;

    $activeChips = collect();

    if (filled($search)) {
        $activeChips->push(['label' => 'Búsqueda', 'value' => '«'.$search.'»', 'url' => $removeFilter(['search'])]);
    }

    if (($filters['department_id'] ?? 0) > 0) {
        $activeChips->push([
            'label' => 'Departamento',
            'value' => $departmentName ?? 'Seleccionado',
            'url' => $removeFilter(['department_id', 'municipality_id']),
        ]);
    }

    if (($filters['municipality_id'] ?? 0) > 0) {
        $activeChips->push([
            'label' => 'Municipio',
            'value' => $municipalityName ?? 'Seleccionado',
            'url' => $removeFilter(['municipality_id']),
        ]);
    }

    if (($filters['center_id'] ?? 0) > 0) {
        $activeChips->push([
            'label' => 'Centro',
            'value' => $centerName ?? 'Seleccionado',
            'url' => $removeFilter(['center_id', 'career_id']),
        ]);
    }

    if (($filters['career_id'] ?? 0) > 0) {
        $activeChips->push([
            'label' => 'Carrera',
            'value' => $careerName ?? 'Seleccionada',
            'url' => $removeFilter(['career_id']),
        ]);
    }

    $hasFilters = $current->isNotEmpty();
    $total = $projects->total();
@endphp

<div class="flex flex-wrap items-end justify-between gap-3">
    <p class="text-[0.8125rem] leading-5 text-slate-500">
        <span class="font-semibold tabular-nums text-slate-900">{{ number_format($total, 0, ',', '.') }}</span>
        {{ $total === 1 ? 'proyecto' : 'proyectos' }}
        {{ $hasFilters ? 'encontrados' : 'publicados' }}
    </p>

    @if ($total > 0)
        <p class="hidden items-center gap-1.5 text-[0.6875rem] text-slate-400 sm:flex">
            <x-ui.icon name="clock" class="h-3.5 w-3.5" />
            Ordenados por fecha de publicación
        </p>
    @endif
</div>

{{-- Specialities: the fastest way to narrow the catalog, so they stay visible. --}}
<div class="mt-3 flex gap-2 overflow-x-auto pb-1 [scrollbar-width:none] sm:flex-wrap sm:overflow-visible [&::-webkit-scrollbar]:hidden">
    <a
        href="{{ route('catalog.index', $categoryBase) }}"
        @class([
            'shrink-0 rounded-full border px-3 py-1.5 text-[0.75rem] font-medium transition-colors duration-150',
            'border-blue-700 bg-blue-700 text-white shadow-[0_1px_1px_rgb(15_23_42/0.08)]' => $selectedCategory === '',
            'border-slate-200 bg-white text-slate-600 hover:border-slate-300 hover:text-slate-900' => $selectedCategory !== '',
        ])
        @if ($selectedCategory === '') aria-current="true" @endif
    >
        Todas
    </a>

    @foreach ($categories as $category)
        <a
            href="{{ route('catalog.index', array_merge($categoryBase, ['category' => $category])) }}"
            @class([
                'shrink-0 rounded-full border px-3 py-1.5 text-[0.75rem] font-medium transition-colors duration-150',
                'border-blue-700 bg-blue-700 text-white shadow-[0_1px_1px_rgb(15_23_42/0.08)]' => $selectedCategory === $category,
                'border-slate-200 bg-white text-slate-600 hover:border-slate-300 hover:text-slate-900' => $selectedCategory !== $category,
            ])
            @if ($selectedCategory === $category) aria-current="true" @endif
        >
            {{ $category }}
        </a>
    @endforeach
</div>

@if ($activeChips->isNotEmpty())
    <div class="mt-4 flex flex-wrap items-center gap-1.5">
        <span class="me-0.5 text-[0.625rem] font-semibold tracking-[0.1em] text-slate-400 uppercase">Filtros activos</span>

        @foreach ($activeChips as $chip)
            <a
                href="{{ $chip['url'] }}"
                class="group inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white py-1 ps-2.5 pe-1.5 text-[0.6875rem] font-medium text-slate-600 transition-[border-color,color] duration-150 hover:border-slate-300 hover:text-slate-900"
                aria-label="Quitar filtro {{ $chip['label'] }}: {{ $chip['value'] }}"
            >
                <span class="text-slate-400">{{ $chip['label'] }}</span>
                <span class="max-w-40 truncate">{{ $chip['value'] }}</span>
                <span class="flex h-4 w-4 items-center justify-center rounded-full bg-slate-100 text-slate-500 transition-colors duration-150 group-hover:bg-slate-200">
                    <x-ui.icon name="x" class="h-2.5 w-2.5" />
                </span>
            </a>
        @endforeach

        <a href="{{ route('catalog.index') }}" class="ms-0.5 rounded-md px-1.5 py-1 text-[0.6875rem] font-medium text-blue-700 underline-offset-4 transition-colors duration-150 hover:text-blue-800 hover:underline">
            Limpiar todo
        </a>
    </div>
@endif

@if ($projects->isEmpty())
    <x-ui.empty-state
        class="mt-6"
        icon="{{ filled($search) ? 'search' : 'grid' }}"
        :title="filled($search) ? 'Sin resultados para «'.$search.'»' : ($hasFilters ? 'No hay proyectos con estos filtros.' : 'Aún no hay proyectos publicados.')"
        :description="filled($search)
            ? 'Prueba con otra palabra o quita algunos filtros para ampliar la búsqueda.'
            : ($hasFilters
                ? 'Combinaste varias condiciones. Prueba quitando una o explora todo el catálogo.'
                : 'Cuando las y los estudiantes publiquen sus proyectos, aparecerán aquí. El catálogo se actualiza en cuanto se publica un proyecto.')"
    >
        @if ($hasFilters)
            <x-ui.button href="{{ route('catalog.index') }}" variant="secondary" icon="x">
                Limpiar filtros
            </x-ui.button>
        @else
            <x-ui.button :href="auth()->check() ? route('projects.create') : route('register')" icon="plus">
                Publicar mi proyecto
            </x-ui.button>
        @endif
    </x-ui.empty-state>
@else
    <div class="mt-6 grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($projects as $project)
            <x-project-card :project="$project" />
        @endforeach
    </div>

    <div class="mt-8">
        <x-ui.pagination :paginator="$projects" />
    </div>
@endif
