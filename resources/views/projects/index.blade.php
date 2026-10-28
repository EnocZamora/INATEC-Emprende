@extends('layouts.public')

@section('title', 'Mi panel')
@section('description', 'Administra tus emprendimientos: revisa su estado, edita la información y publícalos en el catálogo de INATEC Emprende.')

@section('content')
    @php
        $user = auth()->user();
        $firstName = \Illuminate\Support\Str::of($user->name)->explode(' ')->first();
        $profileComplete = filled($profile?->center_id) && filled($profile?->career_id) && filled($profile?->phone_whatsapp);

        $statCards = [
            ['label' => 'Emprendimientos', 'value' => $stats['total'], 'hint' => 'en total', 'tone' => 'text-slate-900'],
            ['label' => 'Publicados', 'value' => $stats['published'], 'hint' => 'visibles en el catálogo', 'tone' => 'text-blue-700'],
            ['label' => 'Borradores', 'value' => $stats['draft'], 'hint' => 'solo los ves tú', 'tone' => 'text-slate-900'],
            ['label' => 'Suspendidos', 'value' => $stats['suspended'], 'hint' => 'fuera del catálogo', 'tone' => $stats['suspended'] > 0 ? 'text-red-700' : 'text-slate-900'],
        ];
    @endphp

    <section class="border-b border-slate-200/80 bg-slate-50/60">
        <div class="mx-auto max-w-6xl px-5 py-8 sm:px-8 sm:py-10">
            <nav class="flex items-center gap-1.5 text-[0.6875rem] font-medium text-slate-500" aria-label="Ruta de navegación">
                <a href="{{ route('home') }}" class="rounded-sm underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline">Inicio</a>
                <x-ui.icon name="chevron-right" class="h-3 w-3 text-slate-300" />
                <span class="text-slate-700" aria-current="page">Mi panel</span>
            </nav>

            <div class="mt-4 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-[0.6875rem] font-semibold tracking-[0.16em] text-blue-700 uppercase">Panel del emprendedor</p>

                    <h1 class="mt-2 text-[1.875rem] leading-[1.1] font-semibold tracking-[-0.03em] text-balance text-slate-900 sm:text-[2.25rem]">
                        Hola, {{ $firstName }}.
                    </h1>

                    <p class="mt-2.5 max-w-xl text-[0.9375rem] leading-6 text-slate-600">
                        Desde aquí administras tus emprendimientos. Publica cuando estén listos y mantén la información al día.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <x-ui.button href="{{ route('catalog.index') }}" variant="secondary" size="sm" icon="grid">
                        Ver catálogo
                    </x-ui.button>

                    <x-ui.button href="{{ route('projects.create') }}" size="sm" icon="plus">
                        Nuevo emprendimiento
                    </x-ui.button>
                </div>
            </div>

            <div class="mt-7 grid grid-cols-2 gap-3 sm:grid-cols-4">
                @foreach ($statCards as $card)
                    <div class="rounded-xl border border-slate-200/80 bg-white p-4">
                        <p class="text-[0.625rem] font-semibold tracking-[0.12em] text-slate-500 uppercase">{{ $card['label'] }}</p>
                        <p class="mt-1.5 text-[1.75rem] leading-none font-semibold tracking-[-0.03em] tabular-nums {{ $card['tone'] }}">
                            {{ $card['value'] }}
                        </p>
                        <p class="mt-1.5 text-[0.6875rem] leading-4 text-slate-500">{{ $card['hint'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <div class="mx-auto max-w-6xl px-5 py-9 sm:px-8 sm:py-10">
        @if ($stats['suspended'] > 0)
            <x-ui.alert variant="warning" title="Tienes {{ $stats['suspended'] }} {{ $stats['suspended'] === 1 ? 'emprendimiento suspendido' : 'emprendimientos suspendidos' }}" class="mb-6">
                Un emprendimiento suspendido no aparece en el catálogo. Revisa su información o consulta con la coordinación de tu centro tecnológico antes de volver a publicarlo.
            </x-ui.alert>
        @endif

        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_18rem] lg:items-start">
            <section class="min-w-0">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <h2 class="text-[1.125rem] font-semibold tracking-[-0.015em] text-slate-900">Tus emprendimientos</h2>
                        <p class="mt-1 text-[0.75rem] text-slate-500">
                            {{ $projects->total() }} {{ $projects->total() === 1 ? 'emprendimiento registrado' : 'emprendimientos registrados' }}
                        </p>
                    </div>

                    @if ($projects->isNotEmpty())
                        <a href="{{ route('projects.create') }}" class="group inline-flex items-center gap-1 rounded-md text-[0.75rem] font-medium text-blue-700 underline-offset-4 transition-colors duration-150 hover:text-blue-800 hover:underline">
                            <x-ui.icon name="plus" class="h-3.5 w-3.5" />
                            Agregar
                        </a>
                    @endif
                </div>

                @if ($projects->isEmpty())
                    <x-ui.empty-state
                        icon="plus"
                        title="Todavía no tienes emprendimientos."
                        description="Publica tu primer emprendimiento técnico. Puedes guardarlo como borrador y publicarlo cuando la información y la portada estén listas."
                        class="mt-4"
                    >
                        <div class="flex flex-col items-center justify-center gap-2 sm:flex-row">
                            <x-ui.button href="{{ route('projects.create') }}" icon="plus">
                                Crear mi primer emprendimiento
                            </x-ui.button>

                            <x-ui.button href="{{ route('catalog.index') }}" variant="secondary">
                                Ver ejemplos del catálogo
                            </x-ui.button>
                        </div>
                    </x-ui.empty-state>
                @else
                    <ul class="mt-4 divide-y divide-slate-200/80 overflow-hidden rounded-xl border border-slate-200/80 bg-white">
                        @foreach ($projects as $project)
                            @php
                                $cover = filled($project->cover_photo)
                                    ? \Illuminate\Support\Facades\Storage::disk('public')->url($project->cover_photo)
                                    : null;
                            @endphp

                            <li class="flex flex-col gap-4 p-4 transition-colors duration-150 hover:bg-slate-50/60 sm:flex-row sm:items-center">
                                <a
                                    href="{{ route('projects.show', $project) }}"
                                    class="relative block aspect-[16/10] w-full shrink-0 overflow-hidden rounded-lg border border-slate-200/80 bg-slate-100 sm:h-16 sm:w-24"
                                    aria-hidden="true"
                                    tabindex="-1"
                                >
                                    <span class="absolute inset-0 flex items-center justify-center text-slate-300">
                                        <x-ui.icon name="photo" class="h-5 w-5" />
                                    </span>

                                    @if ($cover)
                                        <img
                                            src="{{ $cover }}"
                                            alt=""
                                            loading="lazy"
                                            decoding="async"
                                            class="absolute inset-0 h-full w-full object-cover"
                                            onerror="this.remove()"
                                        >
                                    @endif
                                </a>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <x-status-badge :status="$project->status" />

                                        <span class="text-[0.6875rem] text-slate-300" aria-hidden="true">·</span>

                                        <span class="text-[0.6875rem] text-slate-500">{{ $project->category }}</span>
                                    </div>

                                    <h3 class="mt-1.5 text-[0.875rem] leading-5 font-semibold tracking-[-0.01em] text-slate-900">
                                        <a href="{{ route('projects.show', $project) }}" class="rounded-sm underline-offset-4 transition-colors duration-150 hover:text-blue-800 hover:underline">
                                            {{ $project->title }}
                                        </a>
                                    </h3>

                                    <p class="mt-1 text-[0.6875rem] text-slate-500">
                                        @if ($project->gallery_count > 0)
                                            {{ $project->gallery_count }} {{ $project->gallery_count === 1 ? 'foto adicional' : 'fotos adicionales' }}
                                            <span class="mx-1 text-slate-300" aria-hidden="true">·</span>
                                        @endif
                                        Actualizado {{ $project->updated_at?->locale('es')->isoFormat('D MMM YYYY') }}
                                    </p>
                                </div>

                                <div class="flex shrink-0 items-center gap-1.5" x-data="deleteConfirm">
                                    <x-ui.button href="{{ route('projects.show', $project) }}" variant="ghost" size="sm" icon="eye" class="max-sm:flex-1">
                                        Ver
                                    </x-ui.button>

                                    <x-ui.button href="{{ route('projects.edit', $project) }}" variant="secondary" size="sm" icon="pencil-square" class="max-sm:flex-1">
                                        Editar
                                    </x-ui.button>

                                    <button
                                        type="button"
                                        x-show="! confirming"
                                        @click="ask()"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 outline-none transition-colors duration-150 hover:bg-red-50 hover:text-red-700 focus-visible:ring-2 focus-visible:ring-red-500/40 focus-visible:ring-offset-2 motion-safe:active:scale-[0.98]"
                                        aria-label="Eliminar el emprendimiento {{ $project->title }}"
                                    >
                                        <x-ui.icon name="trash" class="h-4 w-4" />
                                    </button>

                                    <div x-show="confirming" x-cloak class="flex items-center gap-1">
                                        <form method="POST" action="{{ route('projects.destroy', $project) }}">
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                x-ref="confirm"
                                                class="inline-flex h-9 items-center justify-center rounded-lg bg-red-600 px-3 text-[0.75rem] font-medium text-white outline-none transition-[background-color,transform] duration-150 ease-out hover:bg-red-700 focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2 motion-safe:active:scale-[0.98]"
                                            >
                                                ¿Eliminar?
                                            </button>
                                        </form>

                                        <button
                                            type="button"
                                            @click="cancel()"
                                            class="inline-flex h-9 items-center justify-center rounded-lg px-2 text-[0.75rem] font-medium text-slate-500 outline-none transition-colors duration-150 hover:bg-slate-100 hover:text-slate-900 focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2"
                                        >
                                            Cancelar
                                        </button>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-6">
                        <x-ui.pagination :paginator="$projects" />
                    </div>

                    <p class="mt-4 text-[0.6875rem] leading-5 text-slate-400">
                        Al eliminar un emprendimiento deja de aparecer en el catálogo y en tu panel. Las imágenes se conservan en el almacenamiento local por si la coordinación necesita restaurarlo.
                    </p>
                @endif
            </section>

            <aside class="space-y-4" aria-label="Información de tu cuenta">
                <div class="rounded-xl border border-slate-200/80 bg-white p-5">
                    <div class="flex items-start justify-between gap-3">
                        <p class="text-[0.625rem] font-semibold tracking-[0.14em] text-slate-500 uppercase">Ficha institucional</p>
                        <x-ui.avatar :name="$user->name" :src="$user->avatarUrl()" size="sm" />
                    </div>

                    <p class="mt-3 text-[0.875rem] font-semibold text-slate-900">{{ $user->name }}</p>
                    <p class="mt-0.5 truncate text-[0.6875rem] text-slate-500">{{ $user->email }}</p>

                    <dl class="mt-4 space-y-3 border-t border-slate-100 pt-4 text-[0.75rem]">
                        <div>
                            <dt class="text-[0.625rem] font-medium tracking-[0.08em] text-slate-400 uppercase">Centro</dt>
                            <dd class="mt-0.5 leading-5 text-slate-700">{{ $profile?->center?->name ?? 'Sin centro registrado' }}</dd>
                        </div>

                        <div>
                            <dt class="text-[0.625rem] font-medium tracking-[0.08em] text-slate-400 uppercase">Carrera</dt>
                            <dd class="mt-0.5 leading-5 text-slate-700">{{ $profile?->career?->name ?? 'Sin carrera registrada' }}</dd>
                        </div>

                        <div>
                            <dt class="text-[0.625rem] font-medium tracking-[0.08em] text-slate-400 uppercase">WhatsApp</dt>
                            <dd class="mt-0.5 tabular-nums leading-5 text-slate-700">{{ $profile?->whatsapp_display ?? 'Sin número registrado' }}</dd>
                        </div>
                    </dl>

                    <div class="mt-4 border-t border-slate-100 pt-3">
                        <x-ui.button href="{{ route('profile.edit') }}" variant="secondary" size="sm" icon="pencil-square" class="w-full">
                            Editar perfil
                        </x-ui.button>

                        @unless ($profileComplete)
                            <p class="mt-2.5 text-[0.6875rem] leading-5 text-slate-500">
                                Tu ficha está incompleta: agrega tu WhatsApp para que los visitantes puedan contactarte.
                            </p>
                        @endunless
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200/80 bg-slate-50/60 p-5">
                    <p class="text-[0.625rem] font-semibold tracking-[0.14em] text-slate-500 uppercase">Antes de publicar</p>

                    <ul class="mt-3 space-y-2.5 text-[0.75rem] leading-5 text-slate-600">
                        <li class="flex gap-2">
                            <x-ui.icon name="check-circle" class="mt-px h-3.5 w-3.5 shrink-0 text-blue-700" />
                            Una portada horizontal, clara y de al menos 1200 px de ancho.
                        </li>
                        <li class="flex gap-2">
                            <x-ui.icon name="check-circle" class="mt-px h-3.5 w-3.5 shrink-0 text-blue-700" />
                            Una descripción que explique el problema, la solución y el estado actual.
                        </li>
                        <li class="flex gap-2">
                            <x-ui.icon name="check-circle" class="mt-px h-3.5 w-3.5 shrink-0 text-blue-700" />
                            Tu número de WhatsApp en la ficha para recibir consultas directas.
                        </li>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
@endsection
