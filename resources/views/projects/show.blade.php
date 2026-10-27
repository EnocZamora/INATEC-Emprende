@extends('layouts.public')

@section('title', $project->title)
@section('description', \Illuminate\Support\Str::limit(strip_tags($project->description), 155))

@section('content')
    @php
        $owner = $project->user;
        $profile = $owner?->profile;
        $center = $profile?->center;
        $municipality = $center?->municipality;
        $department = $municipality?->department;
        $whatsappUrl = $project->whatsapp_url;
        $published = $project->isPublished();
        $cover = filled($project->cover_photo)
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($project->cover_photo)
            : null;
    @endphp

    <section class="border-b border-slate-200/80 bg-slate-50/60">
        <div class="mx-auto max-w-6xl px-5 py-6 sm:px-8 sm:py-8">
            <nav class="flex flex-wrap items-center gap-1.5 text-[0.6875rem] font-medium text-slate-500" aria-label="Ruta de navegación">
                <a href="{{ route('home') }}" class="rounded-sm underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline">Inicio</a>
                <x-ui.icon name="chevron-right" class="h-3 w-3 text-slate-300" />
                <a href="{{ route('catalog.index') }}" class="rounded-sm underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline">Catálogo</a>
                <x-ui.icon name="chevron-right" class="h-3 w-3 text-slate-300" />
                <a href="{{ route('catalog.index', ['category' => $project->category]) }}" class="rounded-sm underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline">{{ $project->category }}</a>
                <x-ui.icon name="chevron-right" class="h-3 w-3 text-slate-300" />
                <span class="max-w-[14rem] truncate text-slate-700" aria-current="page">{{ $project->title }}</span>
            </nav>

            @unless ($published)
                <div class="mt-5 flex gap-3 rounded-xl border border-amber-200 bg-amber-50/70 p-4">
                    <x-ui.icon name="exclamation-triangle" class="mt-px h-4 w-4 shrink-0 text-amber-600" />

                    <div class="min-w-0 text-[0.8125rem] leading-5">
                        <p class="font-semibold text-amber-900">
                            {{ $project->isDraft() ? 'Vista previa de borrador' : 'Proyecto suspendido' }}
                        </p>

                        <p class="mt-0.5 text-amber-800">
                            @if ($project->isDraft())
                                Este proyecto todavía no es visible para el público. Publícalo desde el panel cuando la información y la portada estén listas.
                            @else
                                Este proyecto no aparece en el catálogo público. Revisa la información o contacta a la coordinación de tu centro tecnológico.
                            @endif
                        </p>
                    </div>
                </div>
            @endunless

            <div class="mt-5 flex flex-wrap items-center gap-2">
                <x-ui.badge>{{ $project->category }}</x-ui.badge>

                @unless ($published)
                    <x-status-badge :status="$project->status" />
                @endunless

                <span class="h-3 w-px bg-slate-300" aria-hidden="true"></span>

                <span class="inline-flex items-center gap-1.5 text-[0.6875rem] text-slate-500">
                    <x-ui.icon name="calendar" class="h-3.5 w-3.5" />
                    {{ $project->created_at?->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
                </span>
            </div>

            <h1 class="mt-3 max-w-4xl text-[2rem] leading-[1.1] font-semibold tracking-[-0.03em] text-balance text-slate-900 sm:text-[2.75rem]">
                {{ $project->title }}
            </h1>

            <div class="mt-5 flex flex-wrap items-center gap-x-5 gap-y-2 text-[0.75rem] text-slate-500">
                <span class="flex items-center gap-2">
                    <x-ui.avatar :name="$owner?->name ?? 'INATEC'" size="xs" />
                    <span class="font-medium text-slate-700">{{ $owner?->name ?? 'Estudiante INATEC' }}</span>
                </span>

                @if ($profile?->career)
                    <span class="flex items-center gap-1.5">
                        <x-ui.icon name="academic-cap" class="h-3.5 w-3.5 text-slate-400" />
                        {{ $profile->career->name }}
                    </span>
                @endif

                @if ($center)
                    <span class="flex items-center gap-1.5">
                        <x-ui.icon name="building-office" class="h-3.5 w-3.5 text-slate-400" />
                        {{ $center->name }}
                    </span>
                @endif
            </div>

            @can('update', $project)
                <div class="mt-5 flex flex-wrap items-center gap-2">
                    <x-ui.button href="{{ route('projects.edit', $project) }}" variant="secondary" size="sm" icon="pencil-square">
                        Editar proyecto
                    </x-ui.button>

                    <x-ui.button href="{{ route('dashboard') }}" variant="ghost" size="sm" icon="grid">
                        Mi panel
                    </x-ui.button>
                </div>
            @endcan
        </div>
    </section>

    <div class="mx-auto max-w-6xl px-5 py-10 sm:px-8 sm:py-12">
        <div class="grid gap-10 lg:grid-cols-[minmax(0,1fr)_20rem] lg:items-start">
            <article class="min-w-0">
                @if ($cover)
                    <figure class="overflow-hidden rounded-xl border border-slate-200/80 bg-slate-100 shadow-[0_1px_2px_rgb(15_23_42/0.04),0_24px_48px_-32px_rgb(15_23_42/0.25)]">
                        <img
                            src="{{ $cover }}"
                            alt="Portada del proyecto {{ $project->title }}"
                            class="aspect-[16/10] w-full object-cover text-transparent"
                            decoding="async"
                        >
                    </figure>
                @endif

                <section class="mt-9">
                    <h2 class="text-[1.0625rem] font-semibold tracking-[-0.015em] text-slate-900">Sobre el proyecto</h2>

                    <div class="mt-3 max-w-[46rem] text-[0.9375rem] leading-7 break-words text-pretty text-slate-600">
                        {!! nl2br(e($project->description)) !!}
                    </div>
                </section>

                @if ($project->gallery->isNotEmpty())
                    <section class="mt-10 border-t border-slate-200/80 pt-8">
                        <div class="flex items-baseline justify-between gap-4">
                            <h2 class="text-[1.0625rem] font-semibold tracking-[-0.015em] text-slate-900">Galería</h2>
                            <p class="text-[0.75rem] text-slate-500">
                                {{ $project->gallery->count() }} {{ $project->gallery->count() === 1 ? 'imagen' : 'imágenes' }}
                            </p>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                            @foreach ($project->gallery as $image)
                                <a
                                    href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($image->image_path) }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="group relative aspect-[4/3] overflow-hidden rounded-lg border border-slate-200/80 bg-slate-100 outline-none focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2"
                                    title="Abrir imagen de {{ $project->title }}"
                                >
                                    <img
                                        src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($image->image_path) }}"
                                        alt="{{ $project->title }} — imagen {{ $loop->iteration }}"
                                        loading="lazy"
                                        decoding="async"
                                        class="h-full w-full object-cover text-transparent transition-transform duration-300 ease-out motion-reduce:transition-none motion-safe:group-hover:scale-[1.02]"
                                    >

                                    <span class="absolute end-2 top-2 flex h-6 w-6 items-center justify-center rounded-full bg-white/90 text-slate-600 opacity-0 shadow-[0_1px_2px_rgb(2_6_23/0.12)] transition-opacity duration-150 group-hover:opacity-100 group-focus-visible:opacity-100">
                                        <x-ui.icon name="arrow-up-right" class="h-3.5 w-3.5" />
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif
            </article>

            <aside class="space-y-4 lg:sticky lg:top-20 lg:self-start" aria-label="Contacto y ficha del proyecto">
                <div class="overflow-hidden rounded-xl border border-slate-200/80 bg-white">
                    <div class="border-b border-slate-200/80 bg-slate-50/60 px-4 py-3">
                        <p class="text-[0.625rem] font-semibold tracking-[0.14em] text-slate-500 uppercase">Contacto directo</p>
                    </div>

                    <div class="p-4">
                        <div class="flex items-center gap-3">
                            <x-ui.avatar :name="$owner?->name ?? 'INATEC'" size="lg" />

                            <div class="min-w-0">
                                <p class="truncate text-[0.875rem] font-semibold text-slate-900">{{ $owner?->name ?? 'Estudiante INATEC' }}</p>
                                <p class="mt-0.5 truncate text-[0.6875rem] text-slate-500">
                                    {{ $profile?->career?->name ?? 'Estudiante INATEC' }}
                                </p>
                            </div>
                        </div>

                        @if (filled($profile?->bio))
                            <p class="mt-3 text-[0.8125rem] leading-5 text-slate-600">{{ $profile->bio }}</p>
                        @endif

                        @if ($whatsappUrl)
                            <x-ui.button
                                :href="$whatsappUrl"
                                target="_blank"
                                rel="noopener noreferrer"
                                icon="chat"
                                class="mt-4 w-full"
                            >
                                Escribir por WhatsApp
                            </x-ui.button>

                            @if ($profile?->whatsapp_display)
                                <p class="mt-2 text-center text-[0.6875rem] tabular-nums text-slate-400">
                                    {{ $profile->whatsapp_display }}
                                </p>
                            @endif

                            <p class="mt-1.5 text-center text-[0.625rem] leading-4 text-slate-400">
                                Se abre WhatsApp con un mensaje listo para enviar.
                            </p>
                        @else
                            <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-3">
                                <p class="text-[0.75rem] font-medium text-slate-700">Contacto no disponible</p>
                                <p class="mt-0.5 text-[0.6875rem] leading-5 text-slate-500">
                                    La ficha del estudiante todavía no incluye un número de WhatsApp.
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="border-t border-slate-200/80 p-3" x-data="shareLink({ url: @js(url()->current()) })">
                        <x-ui.button type="button" variant="secondary" class="w-full" @click="copy()">
                            <span x-show="! copied && ! failed" class="inline-flex items-center gap-1.5">
                                <x-ui.icon name="link" class="h-3.5 w-3.5 text-slate-400" />
                                Copiar enlace
                            </span>

                            <span x-show="copied" x-cloak class="inline-flex items-center gap-1.5 text-emerald-700">
                                <x-ui.icon name="check" class="h-3.5 w-3.5" />
                                Enlace copiado
                            </span>

                            <span x-show="failed" x-cloak class="inline-flex items-center gap-1.5 text-red-700">
                                <x-ui.icon name="alert-circle" class="h-3.5 w-3.5" />
                                No se pudo copiar
                            </span>
                        </x-ui.button>

                        <p class="sr-only" role="status" aria-live="polite" x-text="copied ? 'Enlace copiado al portapapeles.' : (failed ? 'No se pudo copiar el enlace.' : '')"></p>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200/80 bg-white p-4">
                    <p class="text-[0.625rem] font-semibold tracking-[0.14em] text-slate-500 uppercase">Ficha del proyecto</p>

                    <dl class="mt-3 divide-y divide-slate-100 text-[0.75rem]">
                        <div class="flex items-start justify-between gap-4 py-2.5">
                            <dt class="text-slate-500">Especialidad</dt>
                            <dd class="text-end font-medium text-slate-900">{{ $project->category }}</dd>
                        </div>

                        @if ($center)
                            <div class="flex items-start justify-between gap-4 py-2.5">
                                <dt class="shrink-0 text-slate-500">Centro</dt>
                                <dd class="text-end text-slate-700">{{ $center->name }}</dd>
                            </div>
                        @endif

                        @if ($profile?->career)
                            <div class="flex items-start justify-between gap-4 py-2.5">
                                <dt class="shrink-0 text-slate-500">Carrera</dt>
                                <dd class="text-end text-slate-700">{{ $profile->career->name }}</dd>
                            </div>
                        @endif

                        @if ($municipality)
                            <div class="flex items-start justify-between gap-4 py-2.5">
                                <dt class="shrink-0 text-slate-500">Ubicación</dt>
                                <dd class="text-end text-slate-700">
                                    {{ $municipality->name }}@if ($department), {{ $department->name }}@endif
                                </dd>
                            </div>
                        @endif

                        <div class="flex items-start justify-between gap-4 py-2.5">
                            <dt class="shrink-0 text-slate-500">Publicado</dt>
                            <dd class="text-end text-slate-700">
                                <time datetime="{{ $project->created_at?->toAtomString() }}">
                                    {{ $project->created_at?->locale('es')->isoFormat('D MMM YYYY') }}
                                </time>
                            </dd>
                        </div>
                    </dl>
                </div>
            </aside>
        </div>
    </div>

    @if ($related->isNotEmpty())
        <section class="border-t border-slate-200/80 bg-slate-50/60">
            <div class="mx-auto max-w-6xl px-5 py-12 sm:px-8 sm:py-14">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-[0.6875rem] font-semibold tracking-[0.16em] text-blue-700 uppercase">Sigue explorando</p>

                        <h2 class="mt-2 text-[1.5rem] leading-tight font-semibold tracking-[-0.025em] text-slate-900">
                            Más proyectos de {{ $project->category }}
                        </h2>
                    </div>

                    <a href="{{ route('catalog.index', ['category' => $project->category]) }}" class="group inline-flex items-center gap-1.5 rounded-md text-[0.8125rem] font-medium text-blue-700 underline-offset-4 transition-colors duration-150 hover:text-blue-800 hover:underline">
                        Ver toda la especialidad
                        <x-ui.icon name="arrow-right" class="h-4 w-4 transition-transform duration-150 group-hover:translate-x-0.5 motion-reduce:transition-none motion-reduce:group-hover:translate-x-0" />
                    </a>
                </div>

                <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($related as $relatedProject)
                        <x-project-card :project="$relatedProject" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
