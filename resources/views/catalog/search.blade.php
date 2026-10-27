@extends('layouts.public')

@section('title', filled($filters['search']) ? 'Búsqueda: '.$filters['search'] : 'Buscar emprendimientos')
@section('description', 'Busca emprendimientos técnicos de egresados certificados por INATEC por título o descripción y combina la búsqueda con filtros de especialidad, centro y departamento.')

@section('content')
    @php $term = $filters['search'] ?? ''; @endphp

    <section class="border-b border-slate-200/80 bg-slate-50/60">
        <div class="mx-auto max-w-6xl px-5 py-9 sm:px-8 sm:py-11">
            <nav class="flex items-center gap-1.5 text-[0.6875rem] font-medium text-slate-500" aria-label="Ruta de navegación">
                <a href="{{ route('home') }}" class="rounded-sm underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline">Inicio</a>
                <x-ui.icon name="chevron-right" class="h-3 w-3 text-slate-300" />
                <a href="{{ route('catalog.index') }}" class="rounded-sm underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline">Catálogo</a>
                <x-ui.icon name="chevron-right" class="h-3 w-3 text-slate-300" />
                <span class="text-slate-700" aria-current="page">Búsqueda</span>
            </nav>

            <div class="mt-4 max-w-3xl">
                <p class="text-[0.6875rem] font-semibold tracking-[0.16em] text-blue-700 uppercase">Búsqueda en el catálogo</p>

                <h1 class="mt-2.5 text-[2rem] leading-[1.1] font-semibold tracking-[-0.03em] text-balance text-slate-900 sm:text-[2.5rem]">
                    @if (filled($term))
                        Resultados para «{{ $term }}»
                    @else
                        Encuentra un emprendimiento
                    @endif
                </h1>

                <p class="mt-3 text-[0.9375rem] leading-6 text-slate-600">
                    @if (filled($term))
                        Buscamos coincidencias en el título y la descripción de todos los emprendimientos publicados. Puedes afinar el resultado con los filtros.
                    @else
                        Escribe una palabra clave, el nombre de una carrera o parte de un título. La búsqueda recorre todos los emprendimientos publicados.
                    @endif
                </p>
            </div>
        </div>
    </section>

    <div class="mx-auto max-w-6xl px-5 py-8 sm:px-8 sm:py-10">
        <x-catalog.browser
            :projects="$projects"
            :departments="$departments"
            :municipalities="$municipalities"
            :centers="$centers"
            :careers="$careers"
            :categories="$categories"
            :filters="$filters"
            :action="route('catalog.search')"
        />
    </div>
@endsection
