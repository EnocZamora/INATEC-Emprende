@extends('layouts.public')

@section('title', 'Editar '.$project->title)
@section('description', 'Actualiza la información, la portada y el estado de tu proyecto en INATEC Emprende.')

@section('content')
    <section class="border-b border-slate-200/80 bg-slate-50/60">
        <div class="mx-auto max-w-6xl px-5 py-8 sm:px-8 sm:py-9">
            <nav class="flex flex-wrap items-center gap-1.5 text-[0.6875rem] font-medium text-slate-500" aria-label="Ruta de navegación">
                <a href="{{ route('home') }}" class="rounded-sm underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline">Inicio</a>
                <x-ui.icon name="chevron-right" class="h-3 w-3 text-slate-300" />
                <a href="{{ route('dashboard') }}" class="rounded-sm underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline">Mi panel</a>
                <x-ui.icon name="chevron-right" class="h-3 w-3 text-slate-300" />
                <span class="max-w-[12rem] truncate text-slate-700" aria-current="page">Editar</span>
            </nav>

            <div class="mt-4 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <div class="max-w-2xl">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="text-[0.6875rem] font-semibold tracking-[0.16em] text-blue-700 uppercase">Editar proyecto</p>
                        <x-status-badge :status="$project->status" />
                    </div>

                    <h1 class="mt-2 text-[1.875rem] leading-[1.1] font-semibold tracking-[-0.03em] text-balance text-slate-900 sm:text-[2.25rem]">
                        {{ $project->title }}
                    </h1>

                    <p class="mt-2.5 text-[0.875rem] leading-6 text-slate-600">
                        Actualizado por última vez el
                        {{ $project->updated_at?->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}.
                        Los cambios se guardan cuando confirmas.
                    </p>
                </div>

                <x-ui.button href="{{ route('projects.show', $project) }}" variant="secondary" size="sm" icon="eye">
                    Ver proyecto
                </x-ui.button>
            </div>
        </div>
    </section>

    <div class="mx-auto max-w-6xl px-5 py-9 sm:px-8 sm:py-10">
        @include('projects._form', [
            'project' => $project,
            'categories' => $categories,
            'profile' => $profile,
        ])

        <section class="mt-8 rounded-xl border border-red-200 bg-red-50/40 p-5 sm:p-6" x-data="deleteConfirm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="max-w-2xl">
                    <h2 class="text-[0.9375rem] font-semibold text-red-900">Eliminar proyecto</h2>
                    <p class="mt-1 text-[0.75rem] leading-5 text-red-800">
                        El proyecto dejará de aparecer en el catálogo y en tu panel. Las imágenes se conservan en el almacenamiento local por si la coordinación necesita restaurarlo.
                    </p>
                </div>

                <div class="shrink-0">
                    <button
                        type="button"
                        x-show="! confirming"
                        @click="ask()"
                        class="inline-flex h-10 items-center justify-center gap-1.5 rounded-lg border border-red-200 bg-white px-4 text-[0.8125rem] font-medium text-red-700 outline-none transition-[background-color,border-color,transform] duration-150 ease-out hover:border-red-300 hover:bg-red-100 focus-visible:ring-2 focus-visible:ring-red-500/40 focus-visible:ring-offset-2 motion-safe:active:scale-[0.98]"
                    >
                        <x-ui.icon name="trash" class="h-4 w-4" />
                        Eliminar proyecto
                    </button>

                    <div x-show="confirming" x-cloak class="flex items-center gap-1.5">
                        <form method="POST" action="{{ route('projects.destroy', $project) }}">
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                x-ref="confirm"
                                class="inline-flex h-10 items-center justify-center rounded-lg bg-red-600 px-4 text-[0.8125rem] font-medium text-white outline-none transition-[background-color,transform] duration-150 ease-out hover:bg-red-700 focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2 motion-safe:active:scale-[0.98]"
                            >
                                Sí, eliminar
                            </button>
                        </form>

                        <button
                            type="button"
                            @click="cancel()"
                            class="inline-flex h-10 items-center justify-center rounded-lg px-3 text-[0.8125rem] font-medium text-slate-600 outline-none transition-colors duration-150 hover:bg-white hover:text-slate-900 focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2"
                        >
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
