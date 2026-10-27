@extends('layouts.auth')

@section('title', 'Crear cuenta')
@section('description', 'Crea tu cuenta institucional para publicar y administrar tus proyectos técnicos en INATEC Emprende.')

@section('aside-eyebrow', 'Registro de estudiantes')
@section('aside-heading', 'Crea tu cuenta y publica lo que construyes.')
@section('aside-copy', 'Una cuenta institucional te permite mostrar tu proyecto, administrar sus fotos y recibir contacto directo de empresas y comunidad.')

@section('aside-points')
    <x-auth.point index="01" title="Vinculada a tu centro">
        Tu perfil queda asociado al centro tecnológico y a la carrera que cursas.
    </x-auth.point>

    <x-auth.point index="02" title="Tu contacto, bajo tu control">
        El número de WhatsApp solo aparece en el botón de contacto de tus proyectos.
    </x-auth.point>

    <x-auth.point index="03" title="Publica y actualiza cuando quieras">
        Edita, pausa o retira tu proyecto sin intermediarios.
    </x-auth.point>
@endsection

@section('content')
    @php
        $selectedCareerId = (int) old('career_id');
        $selectedCenter = $selectedCareerId > 0
            ? $centers->first(fn ($center) => $center->careers->contains('id', $selectedCareerId))
            : null;
    @endphp

    <div class="sm:rounded-2xl sm:border sm:border-slate-200/80 sm:bg-white sm:p-8 sm:shadow-[0_1px_2px_rgb(15_23_42/0.04),0_12px_32px_-24px_rgb(15_23_42/0.18)]">
        <div>
            <p class="text-[0.6875rem] font-semibold tracking-[0.16em] text-blue-700 uppercase">Registro</p>

            <h1 class="mt-2.5 text-[1.625rem] leading-[1.2] font-semibold tracking-[-0.02em] text-slate-900 sm:text-[1.75rem]">
                Crea tu cuenta
            </h1>

            <p class="mt-2 text-[0.875rem] leading-6 text-slate-600">
                Solo necesitas tus datos institucionales. Podrás completar tu perfil más adelante.
            </p>
        </div>

        @if ($errors->any())
            <x-ui.alert variant="error" class="mt-6" title="Revisa los campos marcados">
                {{ $errors->count() === 1 ? 'Hay un dato que necesita tu atención.' : 'Hay '.$errors->count().' datos que necesitan tu atención.' }}
            </x-ui.alert>
        @endif

        @if ($centers->isEmpty())
            <x-ui.alert variant="info" class="mt-6" title="Catálogo académico en preparación">
                Todavía no hay centros y carreras disponibles para el registro. Consulta con la coordinación de tu centro tecnológico.
            </x-ui.alert>

            <a
                href="{{ route('login') }}"
                class="mt-6 inline-flex items-center gap-1.5 rounded-md text-[0.8125rem] font-medium text-blue-700 underline-offset-4 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2"
            >
                <x-ui.icon name="arrow-left" class="h-4 w-4" />
                Volver a iniciar sesión
            </a>
        @else
            <form
                method="POST"
                action="{{ route('register') }}"
                class="mt-7 space-y-4"
                x-data="{ submitting: false }"
                @submit="submitting = true"
                :aria-busy="submitting"
                novalidate
            >
                @csrf

                <x-ui.input
                    name="name"
                    label="Nombre completo"
                    icon="user"
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
                    autocomplete="email"
                    inputmode="email"
                    autocapitalize="none"
                    spellcheck="false"
                    placeholder="nombre.apellido@correo.com"
                    hint="Lo usarás para iniciar sesión y recuperar tu contraseña."
                    maxlength="255"
                    required
                />

                <x-ui.phone-input
                    name="phone_whatsapp"
                    label="WhatsApp"
                    placeholder="88888888"
                    hint="8 dígitos, sin el código de país. Solo se muestra en el botón de contacto de los proyectos que publiques."
                    required
                />

                <div x-data="{ center: @js($selectedCenter?->name) }">
                    <x-ui.select
                        name="career_id"
                        label="Centro y carrera"
                        icon="academic-cap"
                        required
                        @change="center = $event.target.selectedOptions[0]?.dataset.center || null"
                    >
                        <option value="">Selecciona tu carrera</option>

                        @foreach ($centers as $center)
                            <optgroup label="{{ $center->name }}">
                                @foreach ($center->careers as $career)
                                    <option
                                        value="{{ $career->id }}"
                                        data-center="{{ $center->name }}"
                                        @selected((int) old('career_id') === $career->id)
                                    >
                                        {{ $career->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </x-ui.select>

                    <p
                        x-show="center"
                        x-cloak
                        x-transition:enter="transition duration-150 ease-out motion-reduce:transition-none"
                        x-transition:enter-start="opacity-0 -translate-y-0.5"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="mt-2 flex items-start gap-1.5 text-[0.75rem] leading-5 text-slate-500"
                    >
                        <x-ui.icon name="building-office" class="mt-px h-3.5 w-3.5 shrink-0" />
                        <span class="min-w-0 break-words">
                            Centro: <span class="font-medium text-slate-700" x-text="center"></span>
                        </span>
                    </p>
                </div>

                <div class="border-t border-slate-200/80 pt-1">
                    <p class="pt-4 text-[0.6875rem] font-semibold tracking-[0.14em] text-slate-400 uppercase">
                        Seguridad de la cuenta
                    </p>
                </div>

                <x-ui.password-input
                    name="password"
                    label="Contraseña"
                    autocomplete="new-password"
                    placeholder="Crea una contraseña"
                    strength
                    required
                />

                <x-ui.password-input
                    name="password_confirmation"
                    label="Confirmar contraseña"
                    hint="Repite la misma contraseña."
                    match="password"
                    autocomplete="new-password"
                    placeholder="Repite tu contraseña"
                    required
                />

                <div class="pt-1">
                    <x-ui.checkbox name="terms">
                        Acepto los
                        <a href="{{ route('terms') }}" target="_blank" rel="noopener noreferrer" class="font-medium text-slate-700 underline decoration-slate-300 underline-offset-2 transition-colors duration-150 hover:text-blue-700 hover:decoration-blue-300">términos de uso</a>
                        y la
                        <a href="{{ route('privacy') }}" target="_blank" rel="noopener noreferrer" class="font-medium text-slate-700 underline decoration-slate-300 underline-offset-2 transition-colors duration-150 hover:text-blue-700 hover:decoration-blue-300">política de privacidad</a>.
                    </x-ui.checkbox>
                </div>

                <div class="pt-1">
                    <x-ui.submit-button label="Crear cuenta" loading-label="Creando cuenta…" />
                </div>
            </form>

            <div class="mt-6 border-t border-slate-200/80 pt-5 text-center text-[0.8125rem] text-slate-600">
                ¿Ya tienes cuenta?
                <a
                    href="{{ route('login') }}"
                    class="rounded-sm font-medium text-blue-700 underline-offset-4 transition-colors duration-150 hover:text-blue-800 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2"
                >
                    Iniciar sesión
                </a>
            </div>
        @endif
    </div>
@endsection
