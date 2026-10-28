@extends('layouts.public')

@section('title', 'Mi perfil')
@section('description', 'Consulta tu ficha institucional, tu número de WhatsApp y el estado de tu cuenta en INATEC Emprende.')

@section('content')
    @php
        $checks = [
            ['label' => 'Centro tecnológico', 'done' => filled($profile?->center_id)],
            ['label' => 'Carrera', 'done' => filled($profile?->career_id)],
            ['label' => 'WhatsApp', 'done' => filled($profile?->phone_whatsapp)],
        ];

        $completed = collect($checks)->where('done', true)->count();
        $profileComplete = $completed === count($checks);
        $roleLabel = $user->isAdmin() ? 'Administrador' : 'Estudiante';

        $location = collect([
            $profile?->center?->municipality?->name,
            $profile?->center?->municipality?->department?->name,
        ])->filter()->unique()->implode(', ');

        $canManageMedia = $user->isStudent();
        $bannerUrl = $canManageMedia ? $profile?->banner_url : null;
        $avatarUrl = $canManageMedia ? $profile?->avatar_url : null;

        $statCards = [
            ['label' => 'Proyectos', 'value' => $stats['total'], 'hint' => 'en total', 'tone' => 'text-slate-900'],
            ['label' => 'Publicados', 'value' => $stats['published'], 'hint' => 'visibles en el catálogo', 'tone' => 'text-blue-700'],
            ['label' => 'Borradores', 'value' => $stats['draft'], 'hint' => 'solo los ves tú', 'tone' => 'text-slate-900'],
            ['label' => 'Suspendidos', 'value' => $stats['suspended'], 'hint' => 'fuera del catálogo', 'tone' => $stats['suspended'] > 0 ? 'text-red-700' : 'text-slate-900'],
        ];
    @endphp

    <section class="relative isolate overflow-hidden border-b border-slate-900/10 bg-slate-950">
        {{-- The gradient stays underneath so a missing file never shows a broken banner. --}}
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_15%_20%,rgb(37_99_235/0.35),transparent_45%),radial-gradient(circle_at_85%_0%,rgb(14_165_233/0.25),transparent_40%),linear-gradient(135deg,#020617,#0f172a_55%,#1e293b)]"></div>
        <div class="absolute inset-0 -z-10 opacity-20 [background-image:linear-gradient(to_right,rgb(148_163_184/0.12)_1px,transparent_1px),linear-gradient(to_bottom,rgb(148_163_184/0.12)_1px,transparent_1px)] [background-size:48px_48px]"></div>

        @if ($bannerUrl)
            <img
                src="{{ $bannerUrl }}"
                alt=""
                class="absolute inset-0 -z-10 h-full w-full object-cover"
                fetchpriority="high"
                decoding="async"
                onerror="this.remove()"
            >
            <div class="absolute inset-0 -z-10 bg-gradient-to-t from-slate-950/90 via-slate-950/55 to-slate-950/20"></div>
        @endif

        <div class="mx-auto max-w-6xl px-5 py-8 sm:px-8 sm:py-10">
            <nav class="flex flex-wrap items-center gap-1.5 text-[0.6875rem] font-medium text-white/55" aria-label="Ruta de navegación">
                <a href="{{ route('home') }}" class="rounded-sm underline-offset-4 transition-colors duration-150 hover:text-white hover:underline">Inicio</a>
                <x-ui.icon name="chevron-right" class="h-3 w-3 text-white/25" />
                <span class="text-white/85" aria-current="page">Mi perfil</span>
            </nav>

            <div class="mt-6 flex flex-col gap-6 sm:mt-8 sm:flex-row sm:items-end sm:justify-between">
                <div class="flex min-w-0 items-center gap-4 sm:gap-5">
                    <div class="group relative shrink-0">
                        <x-ui.avatar
                            :name="$user->name"
                            :src="$avatarUrl"
                            size="2xl"
                            tone="white"
                            loading="eager"
                            decoding="async"
                            class="h-20 w-20 border-2 border-white/25 shadow-[0_18px_40px_-18px_rgb(0_0_0/0.85)] sm:h-24 sm:w-24"
                        />

                        @if ($canManageMedia)
                            <a
                                href="{{ route('profile.edit') }}#apariencia"
                                class="absolute -end-1 -bottom-1 grid h-8 w-8 place-items-center rounded-full border-4 border-slate-950/70 bg-white text-slate-700 shadow-sm outline-none transition-[transform,background-color,color] duration-150 ease-out hover:bg-blue-50 hover:text-blue-800 focus-visible:ring-2 focus-visible:ring-white/70 motion-safe:active:scale-95"
                                aria-label="Cambiar foto o portada del perfil"
                            >
                                <x-ui.icon name="camera" class="h-4 w-4" />
                            </a>
                        @endif
                    </div>

                    <div class="min-w-0">
                        <p class="text-[0.6875rem] font-semibold tracking-[0.16em] text-blue-200 uppercase">Cuenta institucional</p>

                        <h1 class="mt-1.5 text-[1.875rem] leading-[1.1] font-semibold tracking-[-0.03em] text-balance text-white sm:text-[2.25rem]">
                            {{ $user->name }}
                        </h1>

                        <p class="mt-1.5 max-w-xl text-[0.875rem] leading-6 text-white/70">
                            Tu ficha institucional, el número de contacto que ven los visitantes y el estado de tu cuenta.
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <a
                        href="{{ route('dashboard') }}"
                        class="inline-flex h-9 items-center gap-1.5 rounded-lg px-3 text-[0.8125rem] font-medium text-white/75 outline-none transition-[background-color,color,transform] duration-150 ease-out hover:bg-white/10 hover:text-white focus-visible:ring-2 focus-visible:ring-white/70 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950 motion-safe:active:scale-[0.98]"
                    >
                        <x-ui.icon name="grid" class="h-3.5 w-3.5 opacity-90" />
                        Mi panel
                    </a>

                    <x-ui.button href="{{ route('profile.edit') }}" variant="white" size="sm" icon="pencil-square">
                        Editar perfil
                    </x-ui.button>
                </div>
            </div>
        </div>
    </section>

    <div class="mx-auto max-w-6xl px-5 py-9 sm:px-8 sm:py-10">
        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_18rem] lg:items-start">
            <div class="min-w-0 space-y-4">
                <section class="rounded-xl border border-slate-200/80 bg-white p-5 sm:p-6" aria-labelledby="identidad">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex min-w-0 items-center gap-3.5">
                            <x-ui.avatar :name="$user->name" :src="$avatarUrl" size="lg" />

                            <div class="min-w-0">
                                <h2 id="identidad" class="truncate text-[1rem] leading-6 font-semibold tracking-[-0.015em] text-slate-900">
                                    {{ $user->name }}
                                </h2>
                                <p class="mt-0.5 truncate text-[0.8125rem] text-slate-500">{{ $user->email }}</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-1.5">
                            <x-ui.badge variant="blue">{{ $roleLabel }}</x-ui.badge>
                            <x-ui.badge variant="green" dot>Cuenta activa</x-ui.badge>
                        </div>
                    </div>

                    <dl class="mt-5 grid gap-4 border-t border-slate-100 pt-5 text-[0.8125rem] sm:grid-cols-2">
                        <div>
                            <dt class="text-[0.625rem] font-medium tracking-[0.08em] text-slate-400 uppercase">Correo de acceso</dt>
                            <dd class="mt-1 truncate text-slate-700">{{ $user->email }}</dd>
                        </div>

                        <div>
                            <dt class="text-[0.625rem] font-medium tracking-[0.08em] text-slate-400 uppercase">Miembro desde</dt>
                            <dd class="mt-1 text-slate-700">
                                {{ $user->created_at?->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-[0.625rem] font-medium tracking-[0.08em] text-slate-400 uppercase">Rol</dt>
                            <dd class="mt-1 text-slate-700">{{ $roleLabel }}</dd>
                        </div>

                        <div>
                            <dt class="text-[0.625rem] font-medium tracking-[0.08em] text-slate-400 uppercase">Ficha institucional</dt>
                            <dd class="mt-1 text-slate-700">
                                {{ $profileComplete ? 'Completa' : $completed.' de '.count($checks).' datos' }}
                            </dd>
                        </div>
                    </dl>
                </section>

                <section class="rounded-xl border border-slate-200/80 bg-white p-5 sm:p-6" aria-labelledby="ficha">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 id="ficha" class="text-[0.9375rem] font-semibold tracking-[-0.01em] text-slate-900">Ficha institucional</h2>
                            <p class="mt-1 text-[0.75rem] leading-5 text-slate-500">
                                Los datos con los que apareces junto a tus proyectos.
                            </p>
                        </div>

                        <x-ui.badge :variant="$profileComplete ? 'green' : 'amber'">
                            {{ $profileComplete ? 'Completa' : 'Incompleta' }}
                        </x-ui.badge>
                    </div>

                    <dl class="mt-5 grid gap-4 text-[0.8125rem] sm:grid-cols-2">
                        <div>
                            <dt class="text-[0.625rem] font-medium tracking-[0.08em] text-slate-400 uppercase">Centro tecnológico</dt>
                            <dd class="mt-1 leading-5 text-slate-700">{{ $profile?->center?->name ?? 'Sin centro registrado' }}</dd>
                        </div>

                        <div>
                            <dt class="text-[0.625rem] font-medium tracking-[0.08em] text-slate-400 uppercase">Carrera</dt>
                            <dd class="mt-1 leading-5 text-slate-700">{{ $profile?->career?->name ?? 'Sin carrera registrada' }}</dd>
                        </div>

                        <div>
                            <dt class="text-[0.625rem] font-medium tracking-[0.08em] text-slate-400 uppercase">Ubicación del centro</dt>
                            <dd class="mt-1 leading-5 text-slate-700">{{ $location !== '' ? $location : 'Sin ubicación registrada' }}</dd>
                        </div>

                        <div>
                            <dt class="text-[0.625rem] font-medium tracking-[0.08em] text-slate-400 uppercase">WhatsApp</dt>
                            <dd class="mt-1 tabular-nums leading-5 text-slate-700">{{ $profile?->whatsapp_display ?? 'Sin número registrado' }}</dd>
                        </div>
                    </dl>

                    <div class="mt-5 border-t border-slate-100 pt-5">
                        <p class="text-[0.625rem] font-medium tracking-[0.08em] text-slate-400 uppercase">Descripción personal</p>

                        @if (filled($profile?->bio))
                            <p class="mt-1.5 max-w-prose text-[0.8125rem] leading-6 whitespace-pre-line text-slate-700">{{ $profile->bio }}</p>
                        @else
                            <p class="mt-1.5 text-[0.8125rem] leading-6 text-slate-500">
                                Todavía no has agregado una descripción. Ayuda a quien visita tus proyectos a saber quién está detrás.
                            </p>
                        @endif
                    </div>

                    @if ($canManageMedia && $profile?->diploma_url)
                        <div class="mt-5 border-t border-slate-100 pt-5">
                            <p class="text-[0.625rem] font-medium tracking-[0.08em] text-slate-400 uppercase">Diploma</p>
                            <a
                                href="{{ $profile->diploma_url }}"
                                target="_blank"
                                rel="noopener"
                                class="mt-1.5 inline-flex min-h-9 items-center rounded-md text-[0.8125rem] font-medium text-blue-700 underline-offset-4 outline-none transition-[color,transform] duration-150 ease-out hover:text-blue-800 hover:underline focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2 motion-safe:active:scale-[0.98] motion-reduce:transition-none"
                            >
                                Ver diploma
                            </a>
                        </div>
                    @endif

                    @unless ($profileComplete)
                        <x-ui.alert variant="warning" title="Tu ficha está incompleta" class="mt-5">
                            <p>Completa los datos pendientes para que los visitantes puedan contactarte sin intermediarios.</p>

                            <ul class="mt-1.5 space-y-0.5">
                                @foreach ($checks as $check)
                                    @unless ($check['done'])
                                        <li class="flex items-center gap-1.5">
                                            <x-ui.icon name="alert-circle" class="h-3.5 w-3.5 shrink-0" />
                                            {{ $check['label'] }}
                                        </li>
                                    @endunless
                                @endforeach
                            </ul>

                            <x-ui.button href="{{ route('profile.edit') }}" size="sm" icon="pencil-square" class="mt-3">
                                Completar ficha
                            </x-ui.button>
                        </x-ui.alert>
                    @endunless
                </section>

                <section class="rounded-xl border border-slate-200/80 bg-white p-5 sm:p-6" aria-labelledby="proyectos">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <h2 id="proyectos" class="text-[0.9375rem] font-semibold tracking-[-0.01em] text-slate-900">Tus proyectos</h2>
                            <p class="mt-1 text-[0.75rem] leading-5 text-slate-500">Resumen del escaparate que ya publicaste.</p>
                        </div>

                        <a href="{{ route('dashboard') }}" class="group inline-flex shrink-0 items-center gap-1 rounded-md text-[0.75rem] font-medium text-blue-700 underline-offset-4 transition-colors duration-150 hover:text-blue-800 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2">
                            Ver mis proyectos
                            <x-ui.icon name="arrow-right" class="h-3.5 w-3.5 transition-transform duration-150 group-hover:translate-x-0.5 motion-reduce:transition-none" />
                        </a>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                        @foreach ($statCards as $card)
                            <div class="rounded-lg border border-slate-200/80 bg-slate-50/60 p-3.5">
                                <p class="text-[0.625rem] font-semibold tracking-[0.1em] text-slate-500 uppercase">{{ $card['label'] }}</p>
                                <p class="mt-1 text-[1.375rem] leading-none font-semibold tracking-[-0.03em] tabular-nums {{ $card['tone'] }}">
                                    {{ $card['value'] }}
                                </p>
                                <p class="mt-1.5 text-[0.6875rem] leading-4 text-slate-500">{{ $card['hint'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>

            <aside class="space-y-4" aria-label="Información sobre tu cuenta">
                <div class="rounded-xl border border-slate-200/80 bg-white p-5">
                    <p class="text-[0.625rem] font-semibold tracking-[0.14em] text-slate-500 uppercase">Seguridad</p>

                    <p class="mt-2.5 text-[0.75rem] leading-5 text-slate-600">
                        Cambia tu contraseña periódicamente y usa una distinta a la de otros servicios.
                    </p>

                    <x-ui.button href="{{ route('profile.edit') }}#seguridad" variant="secondary" size="sm" icon="lock-closed" class="mt-3 w-full">
                        Cambiar contraseña
                    </x-ui.button>
                </div>

                <div class="rounded-xl border border-slate-200/80 bg-slate-50/60 p-5">
                    <p class="text-[0.625rem] font-semibold tracking-[0.14em] text-slate-500 uppercase">Qué ve el visitante</p>

                    <ul class="mt-3 space-y-2.5 text-[0.75rem] leading-5 text-slate-600">
                        <li class="flex gap-2">
                            <x-ui.icon name="check-circle" class="mt-px h-3.5 w-3.5 shrink-0 text-blue-700" />
                            Tu WhatsApp solo aparece en el botón de contacto de los proyectos publicados.
                        </li>
                        <li class="flex gap-2">
                            <x-ui.icon name="check-circle" class="mt-px h-3.5 w-3.5 shrink-0 text-blue-700" />
                            Tu correo electrónico nunca se muestra en el catálogo.
                        </li>
                        <li class="flex gap-2">
                            <x-ui.icon name="check-circle" class="mt-px h-3.5 w-3.5 shrink-0 text-blue-700" />
                            El centro y la carrera acompañan a cada proyecto que publicas.
                        </li>
                    </ul>
                </div>

                <div class="rounded-xl border border-slate-200/80 bg-white p-5">
                    <p class="text-[0.625rem] font-semibold tracking-[0.14em] text-slate-500 uppercase">Datos institucionales</p>

                    <p class="mt-2.5 text-[0.75rem] leading-5 text-slate-600">
                        El centro y la carrera se asignan al crear la cuenta. Si necesitas cambiarlos, contacta a la coordinación de tu centro tecnológico.
                    </p>
                </div>
            </aside>
        </div>
    </div>
@endsection
