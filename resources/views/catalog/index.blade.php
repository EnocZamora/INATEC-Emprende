@extends('layouts.public')

@section('title', 'Catálogo de proyectos')
@section('description', 'Catálogo institucional de proyectos de los centros tecnológicos de INATEC. Filtra por especialidad, departamento, centro o carrera y contacta directamente a sus autores.')

@section('content')
    <section class="border-b border-slate-200/80 bg-slate-50/60">
        <div class="mx-auto max-w-6xl px-5 py-9 sm:px-8 sm:py-11">
            <nav class="flex items-center gap-1.5 text-[0.6875rem] font-medium text-slate-500" aria-label="Ruta de navegación">
                <a href="{{ route('home') }}" class="rounded-sm underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline">Inicio</a>
                <x-ui.icon name="chevron-right" class="h-3 w-3 text-slate-300" />
                <span class="text-slate-700" aria-current="page">Catálogo</span>
            </nav>

            <div class="mt-4 grid gap-6 lg:grid-cols-[1fr_auto] lg:items-end">
                <div>
                    <p class="text-[0.6875rem] font-semibold tracking-[0.16em] text-blue-700 uppercase">Talento técnico · Nicaragua</p>

                    <h1 class="mt-2.5 text-[2rem] leading-[1.1] font-semibold tracking-[-0.03em] text-balance text-slate-900 sm:text-[2.5rem]">
                        Catálogo de proyectos
                    </h1>

                    <p class="mt-3 max-w-2xl text-[0.9375rem] leading-6 text-slate-600">
                        Proyectos reales de estudiantes de los centros tecnológicos de INATEC. Combina especialidad, departamento, centro y carrera para encontrar lo que buscas.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-x-3 gap-y-2 rounded-lg border border-slate-200/80 bg-white px-3.5 py-2.5 text-[0.6875rem] text-slate-600 shadow-[0_1px_1px_rgb(15_23_42/0.03)]">
                    <span class="flex items-center gap-1.5">
                        <span class="h-1.5 w-1.5 rounded-full bg-blue-700" aria-hidden="true"></span>
                        <span class="font-semibold tabular-nums text-slate-900">{{ number_format($projects->total(), 0, ',', '.') }}</span>
                        {{ $projects->total() === 1 ? 'proyecto' : 'proyectos' }}
                    </span>

                    <span class="hidden h-4 w-px bg-slate-200 sm:block" aria-hidden="true"></span>

                    <span class="flex items-center gap-1.5">
                        <x-ui.icon name="shield-check" class="h-3.5 w-3.5 text-blue-700" />
                        Sin promoción pagada
                    </span>
                </div>
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
        />
    </div>
@endsection
