@extends('layouts.public')

@section('title', 'Editar perfil')
@section('description', 'Actualiza tus datos personales, tu número de WhatsApp y la contraseña de tu cuenta en INATEC Emprende.')

@section('content')
    @php
        $profileErrorKeys = ['name', 'email', 'phone_whatsapp', 'bio'];
        $passwordErrorKeys = ['current_password', 'password'];

        $profileErrors = collect($profileErrorKeys)->flatMap(fn (string $key): array => $errors->get($key))->all();
        $passwordErrors = collect($passwordErrorKeys)->flatMap(fn (string $key): array => $errors->get($key))->all();

        $location = collect([
            $profile?->center?->municipality?->name,
            $profile?->center?->municipality?->department?->name,
        ])->filter()->unique()->implode(', ');
    @endphp

    <section class="border-b border-slate-200/80 bg-slate-50/60">
        <div class="mx-auto max-w-6xl px-5 py-8 sm:px-8 sm:py-9">
            <nav class="flex flex-wrap items-center gap-1.5 text-[0.6875rem] font-medium text-slate-500" aria-label="Ruta de navegación">
                <a href="{{ route('home') }}" class="rounded-sm underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline">Inicio</a>
                <x-ui.icon name="chevron-right" class="h-3 w-3 text-slate-300" />
                <a href="{{ route('profile.show') }}" class="rounded-sm underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline">Mi perfil</a>
                <x-ui.icon name="chevron-right" class="h-3 w-3 text-slate-300" />
                <span class="text-slate-700" aria-current="page">Editar</span>
            </nav>

            <div class="mt-4 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <div class="max-w-2xl">
                    <p class="text-[0.6875rem] font-semibold tracking-[0.16em] text-blue-700 uppercase">Editar perfil</p>

                    <h1 class="mt-2 text-[1.875rem] leading-[1.1] font-semibold tracking-[-0.03em] text-balance text-slate-900 sm:text-[2.25rem]">
                        Tus datos, tu contacto y tu contraseña.
                    </h1>

                    <p class="mt-2.5 text-[0.875rem] leading-6 text-slate-600">
                        Los cambios de contacto se aplican de inmediato a los proyectos que ya publicaste.
                    </p>
                </div>

                <x-ui.button href="{{ route('profile.show') }}" variant="secondary" size="sm" icon="eye">
                    Ver mi perfil
                </x-ui.button>
            </div>
        </div>
    </section>

    <div class="mx-auto max-w-6xl px-5 py-9 sm:px-8 sm:py-10">
        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_18rem] lg:items-start">
            <div class="min-w-0 space-y-6">
                <form
                    method="POST"
                    action="{{ route('profile.update') }}"
                    x-data="{ submitting: false }"
                    @submit="submitting = true"
                    :aria-busy="submitting"
                    class="scroll-mt-24"
                    novalidate
                >
                    @csrf
                    @method('PUT')

                    @if ($profileErrors !== [])
                        <x-ui.alert variant="error" title="Revisa tus datos" class="mb-6">
                            <ul class="mt-1 list-disc space-y-0.5 ps-4">
                                @foreach ($profileErrors as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </x-ui.alert>
                    @endif

                    <div class="space-y-4">
                        <section class="rounded-xl border border-slate-200/80 bg-white p-5 sm:p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h2 class="text-[0.9375rem] font-semibold tracking-[-0.01em] text-slate-900">Datos personales</h2>
                                    <p class="mt-1 text-[0.75rem] leading-5 text-slate-500">
                                        Tu nombre acompaña a cada proyecto en el catálogo.
                                    </p>
                                </div>

                                <x-ui.badge variant="neutral">Obligatorio</x-ui.badge>
                            </div>

                            <div class="mt-5 space-y-5">
                                <x-ui.input
                                    name="name"
                                    label="Nombre completo"
                                    icon="user"
                                    :value="old('name', $user->name)"
                                    autocomplete="name"
                                    placeholder="María Fernanda López"
                                    maxlength="120"
                                    required
                                    autofocus
                                />

                                <x-ui.input
                                    name="email"
                                    label="Correo electrónico"
                                    type="email"
                                    icon="mail"
                                    :value="old('email', $user->email)"
                                    inputmode="email"
                                    autocapitalize="none"
                                    spellcheck="false"
                                    placeholder="nombre.apellido@correo.com"
                                    hint="Lo usas para iniciar sesión. Si lo cambias, utiliza el nuevo la próxima vez."
                                    maxlength="255"
                                    required
                                />
                            </div>
                        </section>

                        <section class="rounded-xl border border-slate-200/80 bg-white p-5 sm:p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h2 class="text-[0.9375rem] font-semibold tracking-[-0.01em] text-slate-900">Contacto directo</h2>
                                    <p class="mt-1 text-[0.75rem] leading-5 text-slate-500">
                                        El número que abre la conversación desde tus proyectos publicados.
                                    </p>
                                </div>

                                <x-ui.badge variant="blue">WhatsApp</x-ui.badge>
                            </div>

                            <div class="mt-5 space-y-5">
                                <x-ui.phone-input
                                    name="phone_whatsapp"
                                    label="WhatsApp"
                                    :value="old('phone_whatsapp', $profile?->phone_whatsapp)"
                                    placeholder="88888888"
                                    hint="8 dígitos, sin el código de país. Solo aparece en el botón de contacto de tus proyectos publicados."
                                    required
                                />

                                <x-ui.textarea
                                    name="bio"
                                    label="Descripción personal"
                                    :value="old('bio', $profile?->bio)"
                                    rows="4"
                                    maxlength="1000"
                                    placeholder="Cuéntanos qué estudias y qué te interesa construir."
                                    hint="Opcional. Te ayuda a presentarte y da contexto a tu perfil."
                                />
                            </div>
                        </section>

                        <section class="rounded-xl border border-slate-200/80 bg-white p-5 sm:p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h2 class="text-[0.9375rem] font-semibold tracking-[-0.01em] text-slate-900">Ficha institucional</h2>
                                    <p class="mt-1 text-[0.75rem] leading-5 text-slate-500">
                                        Se asigna al crear la cuenta y no se edita desde aquí.
                                    </p>
                                </div>

                                <x-ui.badge variant="neutral">Solo lectura</x-ui.badge>
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

                                <div class="sm:col-span-2">
                                    <dt class="text-[0.625rem] font-medium tracking-[0.08em] text-slate-400 uppercase">Ubicación del centro</dt>
                                    <dd class="mt-1 leading-5 text-slate-700">{{ $location !== '' ? $location : 'Sin ubicación registrada' }}</dd>
                                </div>
                            </dl>

                            <p class="mt-4 flex items-start gap-1.5 border-t border-slate-100 pt-4 text-[0.6875rem] leading-5 text-slate-500">
                                <x-ui.icon name="information-circle" class="mt-px h-3.5 w-3.5 shrink-0 text-slate-400" />
                                Si necesitas cambiar de centro o de carrera, contacta a la coordinación de tu centro tecnológico.
                            </p>
                        </section>
                    </div>

                    <div class="mt-5 flex flex-col-reverse gap-2 border-t border-slate-200/80 pt-5 sm:flex-row sm:items-center sm:justify-end">
                        <x-ui.button href="{{ route('profile.show') }}" variant="ghost">
                            Cancelar
                        </x-ui.button>

                        <x-ui.submit-button label="Guardar cambios" loading-label="Guardando…" class="sm:w-auto sm:min-w-44" />
                    </div>
                </form>

                <section id="seguridad" class="scroll-mt-24 rounded-xl border border-slate-200/80 bg-white p-5 sm:p-6" aria-labelledby="seguridad-titulo">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 id="seguridad-titulo" class="text-[0.9375rem] font-semibold tracking-[-0.01em] text-slate-900">Seguridad de la cuenta</h2>
                            <p class="mt-1 text-[0.75rem] leading-5 text-slate-500">
                                Confirma tu contraseña actual antes de definir una nueva.
                            </p>
                        </div>

                        <x-ui.badge variant="neutral">Contraseña</x-ui.badge>
                    </div>

                    @if ($passwordErrors !== [])
                        <x-ui.alert variant="error" title="No pudimos cambiar la contraseña" class="mt-5">
                            <ul class="mt-1 list-disc space-y-0.5 ps-4">
                                @foreach ($passwordErrors as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </x-ui.alert>
                    @endif

                    <form
                        method="POST"
                        action="{{ route('profile.password.update') }}"
                        class="mt-5 space-y-5"
                        x-data="{ submitting: false }"
                        @submit="submitting = true"
                        :aria-busy="submitting"
                        novalidate
                    >
                        @csrf
                        @method('PUT')

                        <x-ui.password-input
                            name="current_password"
                            label="Contraseña actual"
                            hint="La que usas hoy para iniciar sesión."
                            autocomplete="current-password"
                            placeholder="Tu contraseña actual"
                            required
                        />

                        <div class="border-t border-slate-200/80 pt-5">
                            <x-ui.password-input
                                name="password"
                                label="Nueva contraseña"
                                autocomplete="new-password"
                                placeholder="Crea una contraseña nueva"
                                strength
                                required
                            />

                            <div class="mt-5">
                                <x-ui.password-input
                                    name="password_confirmation"
                                    label="Confirmar contraseña nueva"
                                    hint="Repite la misma contraseña."
                                    match="password"
                                    autocomplete="new-password"
                                    placeholder="Repite tu contraseña nueva"
                                    required
                                />
                            </div>
                        </div>

                        <div class="flex justify-end border-t border-slate-200/80 pt-5">
                            <x-ui.submit-button label="Actualizar contraseña" loading-label="Actualizando…" class="sm:w-auto sm:min-w-52" />
                        </div>
                    </form>
                </section>
            </div>

            <aside class="space-y-4" aria-label="Vista previa de tu ficha">
                <div class="rounded-xl border border-slate-200/80 bg-white p-5">
                    <p class="text-[0.625rem] font-semibold tracking-[0.14em] text-slate-500 uppercase">Así se ve tu contacto</p>

                    <div class="mt-3 flex items-center gap-3">
                        <x-ui.avatar :name="$user->name" size="md" />

                        <div class="min-w-0">
                            <p class="truncate text-[0.8125rem] font-semibold text-slate-900">{{ $user->name }}</p>
                            <p class="mt-0.5 truncate text-[0.6875rem] text-slate-500">{{ $profile?->career?->name ?? 'Sin carrera registrada' }}</p>
                        </div>
                    </div>

                    <dl class="mt-4 space-y-3 border-t border-slate-100 pt-4 text-[0.75rem]">
                        <div>
                            <dt class="text-[0.625rem] font-medium tracking-[0.08em] text-slate-400 uppercase">Centro</dt>
                            <dd class="mt-0.5 leading-5 text-slate-700">{{ $profile?->center?->name ?? 'Sin centro registrado' }}</dd>
                        </div>

                        <div>
                            <dt class="text-[0.625rem] font-medium tracking-[0.08em] text-slate-400 uppercase">WhatsApp</dt>
                            <dd class="mt-0.5 tabular-nums leading-5 text-slate-700">{{ $profile?->whatsapp_display ?? 'Sin número registrado' }}</dd>
                        </div>
                    </dl>

                    <p class="mt-4 border-t border-slate-100 pt-3 text-[0.6875rem] leading-5 text-slate-500">
                        Los cambios se reflejan al guardar; la vista previa se actualiza al volver a esta página.
                    </p>
                </div>

                <div class="rounded-xl border border-slate-200/80 bg-slate-50/60 p-5">
                    <p class="text-[0.625rem] font-semibold tracking-[0.14em] text-slate-500 uppercase">Privacidad</p>

                    <ul class="mt-3 space-y-2.5 text-[0.75rem] leading-5 text-slate-600">
                        <li class="flex gap-2">
                            <x-ui.icon name="shield-check" class="mt-px h-3.5 w-3.5 shrink-0 text-blue-700" />
                            Tu correo electrónico nunca se muestra en el catálogo.
                        </li>
                        <li class="flex gap-2">
                            <x-ui.icon name="shield-check" class="mt-px h-3.5 w-3.5 shrink-0 text-blue-700" />
                            El WhatsApp solo se abre desde el botón de contacto de tus proyectos.
                        </li>
                        <li class="flex gap-2">
                            <x-ui.icon name="shield-check" class="mt-px h-3.5 w-3.5 shrink-0 text-blue-700" />
                            Tu contraseña se guarda cifrada y nunca se muestra completa.
                        </li>
                    </ul>
                </div>

                <p class="px-1 text-[0.625rem] leading-5 text-slate-400">
                    El centro y la carrera son datos institucionales: se gestionan con la coordinación de tu centro tecnológico.
                </p>
            </aside>
        </div>
    </div>
@endsection
