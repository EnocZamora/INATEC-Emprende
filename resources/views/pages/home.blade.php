@extends('layouts.public')

@section('title', 'Proyectos estudiantiles de Nicaragua')
@section('description', 'Explora los proyectos de los centros tecnológicos de INATEC por especialidad, centro o departamento y contacta directamente a sus autores por WhatsApp.')

@section('content')
    @php
        $featured = $projects->getCollection()->take(6);
        $total = $projects->total();
        $ctaUrl = auth()->check() ? route('projects.create') : route('register');
    @endphp

    {{-- Hero: the proposition and the current state of the catalog, side by side. --}}
    <section class="relative border-b border-slate-200/80">
        <div class="mx-auto max-w-6xl px-5 py-14 sm:px-8 sm:py-16 lg:py-20">
            <div class="grid items-start gap-12 lg:grid-cols-[1.12fr_0.88fr] lg:gap-16">
                <div class="max-w-[38rem]">
                    <p class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-[0.6875rem] font-medium text-slate-600">
                        <span class="h-1.5 w-1.5 rounded-full bg-blue-700" aria-hidden="true"></span>
                        INATEC Emprende · Tecnológico Nacional
                    </p>

                    <h1 class="mt-5 text-[2.5rem] leading-[1.06] font-semibold tracking-[-0.035em] text-balance text-slate-900 sm:text-[3.25rem] lg:text-[3.5rem]">
                        Proyectos estudiantiles que ya resuelven problemas reales.
                    </h1>

                    <p class="mt-5 max-w-[34rem] text-pretty text-[1rem] leading-7 text-slate-600">
                        El catálogo institucional de los centros tecnológicos de INATEC. Explora por especialidad, centro o departamento y habla directamente con las y los estudiantes que están detrás de cada proyecto.
                    </p>

                    <div class="mt-8 flex flex-col gap-2.5 sm:flex-row sm:items-center">
                        <x-ui.button href="{{ route('catalog.index') }}" size="lg" trailing="arrow-right">
                            Explorar el catálogo
                        </x-ui.button>

                        <x-ui.button href="{{ $ctaUrl }}" size="lg" variant="secondary" icon="plus">
                            Publicar mi proyecto
                        </x-ui.button>
                    </div>

                    <ul class="mt-8 flex flex-wrap items-center gap-x-5 gap-y-2 text-[0.75rem] text-slate-500">
                        <li class="flex items-center gap-1.5">
                            <x-ui.icon name="check" class="h-3.5 w-3.5 text-blue-700" />
                            Sin promoción pagada
                        </li>
                        <li class="flex items-center gap-1.5">
                            <x-ui.icon name="check" class="h-3.5 w-3.5 text-blue-700" />
                            Contacto directo por WhatsApp
                        </li>
                        <li class="flex items-center gap-1.5">
                            <x-ui.icon name="check" class="h-3.5 w-3.5 text-blue-700" />
                            Proyectos de estudiantes INATEC
                        </li>
                    </ul>
                </div>

                <aside class="relative" aria-label="Estado del catálogo">
                    <div class="rounded-2xl border border-slate-200/80 bg-white p-2 shadow-[0_1px_2px_rgb(15_23_42/0.04),0_32px_64px_-48px_rgb(15_23_42/0.35)]">
                        <div class="rounded-xl border border-slate-200/60 bg-slate-50/60 p-5 sm:p-6">
                            <div class="flex items-center justify-between gap-4">
                                <p class="text-[0.6875rem] font-semibold tracking-[0.14em] text-slate-500 uppercase">
                                    El catálogo hoy
                                </p>

                                <x-ui.badge variant="green" dot>Activo</x-ui.badge>
                            </div>

                            <div class="mt-5">
                                <p class="text-[3.25rem] leading-none font-semibold tracking-[-0.045em] tabular-nums text-slate-900">
                                    {{ number_format($total, 0, ',', '.') }}
                                </p>
                                <p class="mt-2 text-[0.8125rem] text-slate-500">
                                    {{ $total === 1 ? 'proyecto publicado' : 'proyectos publicados' }}
                                </p>
                            </div>

                            <div class="mt-6 grid grid-cols-2 gap-px overflow-hidden rounded-lg border border-slate-200/80 bg-slate-200/80">
                                <div class="bg-white px-4 py-3.5">
                                    <p class="text-[1.25rem] leading-none font-semibold tabular-nums text-slate-900">{{ $centers->count() }}</p>
                                    <p class="mt-1.5 text-[0.6875rem] text-slate-500">centros tecnológicos</p>
                                </div>

                                <div class="bg-white px-4 py-3.5">
                                    <p class="text-[1.25rem] leading-none font-semibold tabular-nums text-slate-900">{{ $departments->count() }}</p>
                                    <p class="mt-1.5 text-[0.6875rem] text-slate-500">departamentos</p>
                                </div>
                            </div>

                            <div class="mt-5 border-t border-slate-200/80 pt-5">
                                <p class="text-[0.75rem] font-medium text-slate-700">Especialidades</p>

                                <div class="mt-2.5 flex flex-wrap gap-1.5">
                                    @foreach (array_slice($categories, 0, 3) as $category)
                                        <a href="{{ route('catalog.index', ['category' => $category]) }}" class="rounded-full border border-slate-200 bg-white px-2.5 py-1 text-[0.6875rem] font-medium text-slate-600 transition-colors duration-150 hover:border-slate-300 hover:text-slate-900">
                                            {{ $category }}
                                        </a>
                                    @endforeach

                                    @if (count($categories) > 3)
                                        <a href="{{ route('catalog.index') }}" class="rounded-full border border-transparent bg-transparent px-2 py-1 text-[0.6875rem] font-medium text-blue-700 transition-colors duration-150 hover:text-blue-800">
                                            +{{ count($categories) - 3 }} más
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <p class="mt-5 flex items-start gap-2 border-t border-slate-200/80 pt-4 text-[0.6875rem] leading-5 text-slate-500">
                                <x-ui.icon name="shield-check" class="mt-px h-3.5 w-3.5 shrink-0 text-blue-700" />
                                Orden cronológico y filtros por especialidad. Ninguna posición se compra.
                            </p>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    {{-- How the showcase works. Three steps, no feature list. --}}
    <section id="como-funciona" class="scroll-mt-20 border-b border-slate-200/80">
        <div class="mx-auto max-w-6xl px-5 py-14 sm:px-8 sm:py-16">
            <div class="max-w-2xl">
                <p class="text-[0.6875rem] font-semibold tracking-[0.16em] text-blue-700 uppercase">Cómo funciona</p>

                <h2 class="mt-2.5 text-[1.75rem] leading-[1.15] font-semibold tracking-[-0.025em] text-balance text-slate-900 sm:text-[2rem]">
                    Publicar toma minutos. Encontrar talento, también.
                </h2>

                <p class="mt-3 text-[0.9375rem] leading-6 text-slate-600">
                    El recorrido es corto y transparente: la cuenta es institucional, el proyecto lo administra su autor y el contacto ocurre sin intermediarios.
                </p>
            </div>

            <div class="mt-10 grid gap-8 md:grid-cols-3 md:gap-0 md:divide-x md:divide-slate-200/80">
                <div class="md:pe-8">
                    <div class="flex items-center gap-3">
                        <span class="text-[0.6875rem] font-semibold tracking-[0.1em] tabular-nums text-slate-400">01</span>
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-blue-700">
                            <x-ui.icon name="academic-cap" class="h-4 w-4" />
                        </span>
                    </div>

                    <h3 class="mt-4 text-[0.9375rem] font-semibold text-slate-900">Crea tu cuenta institucional</h3>
                    <p class="mt-1.5 text-[0.8125rem] leading-6 text-slate-500">
                        Regístrate con tu centro y carrera. El acceso está reservado a estudiantes y personal de INATEC.
                    </p>
                </div>

                <div class="md:px-8">
                    <div class="flex items-center gap-3">
                        <span class="text-[0.6875rem] font-semibold tracking-[0.1em] tabular-nums text-slate-400">02</span>
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-blue-700">
                            <x-ui.icon name="pencil-square" class="h-4 w-4" />
                        </span>
                    </div>

                    <h3 class="mt-4 text-[0.9375rem] font-semibold text-slate-900">Publica tu proyecto</h3>
                    <p class="mt-1.5 text-[0.8125rem] leading-6 text-slate-500">
                        Título, descripción, categoría y una portada. Puedes guardarlo como borrador y publicarlo cuando esté listo.
                    </p>
                </div>

                <div class="md:ps-8">
                    <div class="flex items-center gap-3">
                        <span class="text-[0.6875rem] font-semibold tracking-[0.1em] tabular-nums text-slate-400">03</span>
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-blue-700">
                            <x-ui.icon name="chat" class="h-4 w-4" />
                        </span>
                    </div>

                    <h3 class="mt-4 text-[0.9375rem] font-semibold text-slate-900">Recibe contacto directo</h3>
                    <p class="mt-1.5 text-[0.8125rem] leading-6 text-slate-500">
                        Quien se interese te escribe por WhatsApp con el nombre del proyecto en el mensaje. Sin comisiones.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Published projects. Six is enough to show breadth without turning the home into the catalog. --}}
    <section id="catalogo" class="scroll-mt-20">
        <div class="mx-auto max-w-6xl px-5 py-14 sm:px-8 sm:py-16">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-[0.6875rem] font-semibold tracking-[0.16em] text-blue-700 uppercase">Catálogo</p>

                    <h2 class="mt-2.5 text-[1.75rem] leading-[1.15] font-semibold tracking-[-0.025em] text-slate-900 sm:text-[2rem]">
                        Proyectos publicados
                    </h2>

                    <p class="mt-2 max-w-xl text-[0.875rem] leading-6 text-slate-500">
                        Los más recientes. El orden es cronológico: nadie puede pagar por aparecer primero.
                    </p>
                </div>

                <x-ui.button href="{{ route('catalog.index') }}" variant="secondary" trailing="arrow-right">
                    Ver catálogo completo
                </x-ui.button>
            </div>

            <div class="mt-7 flex gap-2 overflow-x-auto pb-1 [scrollbar-width:none] sm:flex-wrap sm:overflow-visible [&::-webkit-scrollbar]:hidden">
                <a href="{{ route('catalog.index') }}" class="shrink-0 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-[0.75rem] font-medium text-slate-600 transition-colors duration-150 hover:border-slate-300 hover:text-slate-900">
                    Todas
                </a>

                @foreach ($categories as $category)
                    <a href="{{ route('catalog.index', ['category' => $category]) }}" class="shrink-0 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-[0.75rem] font-medium text-slate-600 transition-colors duration-150 hover:border-slate-300 hover:text-slate-900">
                        {{ $category }}
                    </a>
                @endforeach
            </div>

            @if ($featured->isEmpty())
                <x-ui.empty-state
                    icon="grid"
                    title="El catálogo está esperando su primer proyecto."
                    description="Si estudias en un centro tecnológico de INATEC, publica tu proyecto y aparecerá aquí para que toda la comunidad pueda conocerlo."
                    class="mt-7"
                >
                    <x-ui.button href="{{ $ctaUrl }}" icon="plus">Publicar mi proyecto</x-ui.button>
                </x-ui.empty-state>
            @else
                <div class="mt-7 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($featured as $project)
                        <x-project-card :project="$project" />
                    @endforeach
                </div>

                @if ($total > $featured->count())
                    <div class="mt-8 flex items-center justify-center">
                        <a href="{{ route('catalog.index') }}" class="group inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-[0.8125rem] font-medium text-blue-700 underline-offset-4 transition-colors duration-150 hover:text-blue-800 hover:underline">
                            Ver los {{ $total }} proyectos del catálogo
                            <x-ui.icon name="arrow-right" class="h-4 w-4 transition-transform duration-150 group-hover:translate-x-0.5 motion-reduce:transition-none motion-reduce:group-hover:translate-x-0" />
                        </a>
                    </div>
                @endif
            @endif
        </div>
    </section>

    {{-- Closing call to action, solid institutional blue: no gradient, no gloss. --}}
    <section class="border-t border-slate-200/80 bg-slate-50/60">
        <div class="mx-auto max-w-6xl px-5 py-14 sm:px-8 sm:py-16">
            <div class="rounded-2xl bg-blue-700 px-6 py-10 sm:px-10 sm:py-12">
                <div class="grid gap-8 lg:grid-cols-[1fr_auto] lg:items-center lg:gap-14">
                    <div class="max-w-2xl">
                        <p class="text-[0.6875rem] font-semibold tracking-[0.16em] text-blue-200 uppercase">Para estudiantes</p>

                        <h2 class="mt-3 text-[1.75rem] leading-[1.15] font-semibold tracking-[-0.025em] text-balance text-white sm:text-[2.125rem]">
                            Tu proyecto merece salir del aula.
                        </h2>

                        <p class="mt-3 max-w-xl text-[0.9375rem] leading-6 text-blue-100">
                            Publicarlo es gratuito, no requiere intermediarios y no altera el orden del catálogo. Solo necesitas tu cuenta institucional y una foto de portada.
                        </p>
                    </div>

                    <div class="flex flex-col items-start gap-3 sm:flex-row sm:items-center lg:flex-col lg:items-stretch">
                        <x-ui.button href="{{ $ctaUrl }}" variant="white" size="lg" icon="plus">
                            Publicar mi proyecto
                        </x-ui.button>

                        <a href="{{ route('terms') }}" class="inline-flex items-center justify-center rounded-lg px-3 py-2 text-[0.8125rem] font-medium text-blue-100 underline-offset-4 transition-colors duration-150 hover:text-white hover:underline">
                            Leer los términos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
