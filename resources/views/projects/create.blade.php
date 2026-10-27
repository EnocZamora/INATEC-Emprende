@extends('layouts.public')

@section('title', 'Nuevo proyecto')
@section('description', 'Publica un proyecto estudiantil en el catálogo de INATEC Emprende. Puedes guardarlo como borrador antes de hacerlo público.')

@section('content')
    <section class="border-b border-slate-200/80 bg-slate-50/60">
        <div class="mx-auto max-w-6xl px-5 py-8 sm:px-8 sm:py-9">
            <nav class="flex flex-wrap items-center gap-1.5 text-[0.6875rem] font-medium text-slate-500" aria-label="Ruta de navegación">
                <a href="{{ route('home') }}" class="rounded-sm underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline">Inicio</a>
                <x-ui.icon name="chevron-right" class="h-3 w-3 text-slate-300" />
                <a href="{{ route('dashboard') }}" class="rounded-sm underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline">Mi panel</a>
                <x-ui.icon name="chevron-right" class="h-3 w-3 text-slate-300" />
                <span class="text-slate-700" aria-current="page">Nuevo proyecto</span>
            </nav>

            <div class="mt-4 max-w-2xl">
                <p class="text-[0.6875rem] font-semibold tracking-[0.16em] text-blue-700 uppercase">Nuevo proyecto</p>

                <h1 class="mt-2 text-[1.875rem] leading-[1.1] font-semibold tracking-[-0.03em] text-balance text-slate-900 sm:text-[2.25rem]">
                    Presenta tu proyecto.
                </h1>

                <p class="mt-2.5 text-[0.9375rem] leading-6 text-slate-600">
                    Completa la información básica y elige una portada. Si aún estás ajustando detalles, guárdalo como borrador.
                </p>
            </div>
        </div>
    </section>

    <div class="mx-auto max-w-6xl px-5 py-9 sm:px-8 sm:py-10">
        @include('projects._form', [
            'project' => null,
            'categories' => $categories,
            'profile' => $profile,
        ])
    </div>
@endsection
