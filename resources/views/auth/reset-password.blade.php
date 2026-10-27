@extends('layouts.auth')

@section('title', 'Nueva contraseña')
@section('description', 'Define una contraseña nueva para tu cuenta de INATEC Emprende.')

@section('aside-eyebrow', 'Seguridad de la cuenta')
@section('aside-heading', 'Define una contraseña nueva y vuelve a entrar.')
@section('aside-copy', 'El enlace que abriste es personal y de un solo uso. Apenas guardes la contraseña, dejará de funcionar.')

@section('aside-points')
    <x-auth.point index="01" title="Mínimo 8 caracteres">
        Combina letras y números. Evita fechas, nombres o contraseñas que ya uses en otros sitios.
    </x-auth.point>

    <x-auth.point index="02" title="El enlace caduca">
        Cada enlace vence a los 60 minutos y solo sirve para una recuperación.
    </x-auth.point>

    <x-auth.point index="03" title="Nadie más la ve">
        La contraseña se guarda cifrada; ni el personal de INATEC puede leerla.
    </x-auth.point>
@endsection

@section('content')
    <div class="sm:rounded-2xl sm:border sm:border-slate-200/80 sm:bg-white sm:p-8 sm:shadow-[0_1px_2px_rgb(15_23_42/0.04),0_12px_32px_-24px_rgb(15_23_42/0.18)]">
        <div>
            <p class="text-[0.6875rem] font-semibold tracking-[0.16em] text-blue-700 uppercase">Nueva contraseña</p>

            <h1 class="mt-2.5 text-[1.625rem] leading-[1.2] font-semibold tracking-[-0.02em] text-slate-900 sm:text-[1.75rem]">
                Crea tu contraseña
            </h1>

            <p class="mt-2 text-[0.875rem] leading-6 text-slate-600">
                Elige una contraseña distinta a la anterior y que no uses en otros servicios.
            </p>
        </div>

        @if ($errors->has('token') || $errors->has('email'))
            <x-ui.alert variant="error" class="mt-6" title="El enlace ya no es válido">
                {{ $errors->first('email') ?: $errors->first('token') ?: 'Solicita un enlace nuevo para continuar.' }}
            </x-ui.alert>
        @endif

        @if ($email === '')
            <div class="mt-6">
                <a
                    href="{{ route('password.request') }}"
                    class="inline-flex h-11 w-full items-center justify-center gap-1.5 rounded-lg bg-blue-700 px-4 text-[0.875rem] font-medium text-white shadow-[0_1px_1px_rgb(15_23_42/0.08)] outline-none transition-[background-color,transform] duration-150 ease-out hover:bg-blue-800 focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 motion-safe:active:scale-[0.99]"
                >
                    Solicitar un enlace nuevo
                </a>

                <p class="mt-3 text-center text-[0.75rem] leading-5 text-slate-500">
                    Abriste el enlace incompleto. Vuelve a solicitar la recuperación desde el inicio.
                </p>
            </div>
        @else
            <div class="mt-6 flex items-center justify-between gap-3 rounded-lg border border-slate-200/80 bg-slate-50/60 px-3.5 py-2.5">
                <div class="flex min-w-0 items-center gap-2">
                    <x-ui.icon name="mail" class="h-4 w-4 shrink-0 text-slate-400" />
                    <p class="min-w-0 truncate text-[0.8125rem] font-medium text-slate-800" title="{{ $email }}">
                        {{ $email }}
                    </p>
                </div>

                <a
                    href="{{ route('password.request') }}"
                    class="shrink-0 rounded-sm text-[0.75rem] font-medium text-slate-500 underline-offset-4 transition-colors duration-150 hover:text-blue-700 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2"
                >
                    Cambiar
                </a>
            </div>

            <form
                method="POST"
                action="{{ route('password.store') }}"
                class="mt-6 space-y-4"
                x-data="{ submitting: false }"
                @submit="submitting = true"
                :aria-busy="submitting"
                novalidate
            >
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <x-ui.password-input
                    name="password"
                    label="Contraseña nueva"
                    autocomplete="new-password"
                    placeholder="Crea una contraseña"
                    strength
                    required
                    autofocus
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
                    <x-ui.submit-button label="Guardar contraseña" loading-label="Guardando…" />
                </div>
            </form>

            <div class="mt-6 flex items-center gap-2.5 border-t border-slate-200/80 pt-5 text-[0.75rem] leading-5 text-slate-500">
                <x-ui.icon name="shield-check" class="h-4 w-4 shrink-0 text-slate-400" />
                <p>Al guardarla, este enlace deja de funcionar y deberás iniciar sesión de nuevo.</p>
            </div>
        @endif
    </div>

    <p class="mt-5 px-1 text-center text-[0.75rem] leading-5 text-slate-500 sm:mt-6">
        ¿Prefieres volver?
        <a
            href="{{ route('login') }}"
            class="rounded-sm font-medium text-slate-700 underline-offset-4 transition-colors duration-150 hover:text-blue-700 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2"
        >
            Iniciar sesión
        </a>
    </p>
@endsection
