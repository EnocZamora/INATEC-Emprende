@extends('layouts.auth')

@section('title', 'Recuperar contraseña')
@section('description', 'Recibe un enlace seguro para restablecer la contraseña de tu cuenta en INATEC Emprende.')

@section('aside-eyebrow', 'Recuperación de acceso')
@section('aside-heading', 'Recupera tu acceso y vuelve a publicar.')
@section('aside-copy', 'Enviamos un enlace personal a tu correo. Tú eliges la contraseña nueva; nadie más la ve ni la administra.')

@section('aside-points')
    <x-auth.point index="01" title="Enlace de un solo uso">
        Vence a los 60 minutos y deja de funcionar en cuanto guardas la contraseña nueva.
    </x-auth.point>

    <x-auth.point index="02" title="Tu contraseña no cambia sola">
        Solo se actualiza cuando abres el enlace y confirmas el cambio tú mismo.
    </x-auth.point>

    <x-auth.point index="03" title="Nunca te pediremos la contraseña">
        El personal de INATEC no la solicita por correo, teléfono ni WhatsApp.
    </x-auth.point>
@endsection

@section('content')
    @php
        $sent = session('status') !== null;
        $email = old('email');
    @endphp

    <div class="sm:rounded-2xl sm:border sm:border-slate-200/80 sm:bg-white sm:p-8 sm:shadow-[0_1px_2px_rgb(15_23_42/0.04),0_12px_32px_-24px_rgb(15_23_42/0.18)]">
        @if ($sent)
            <div class="flex h-11 w-11 items-center justify-center rounded-xl border border-emerald-200/80 bg-emerald-50 text-emerald-600">
                <x-ui.icon name="mail" class="h-5 w-5" />
            </div>

            <h1 class="mt-5 text-[1.625rem] leading-[1.2] font-semibold tracking-[-0.02em] text-slate-900 sm:text-[1.75rem]">
                Revisa tu correo
            </h1>

            <p class="mt-2 text-[0.875rem] leading-6 text-slate-600">
                @if ($email)
                    Si <span class="font-medium break-all text-slate-900">{{ $email }}</span> pertenece a una cuenta, enviamos un enlace para restablecer la contraseña. Puede tardar un par de minutos.
                @else
                    Si el correo pertenece a una cuenta, enviamos un enlace para restablecer la contraseña. Puede tardar un par de minutos.
                @endif
            </p>

            <div class="mt-6 divide-y divide-slate-200/80 border-y border-slate-200/80">
                <div class="flex gap-3 py-3">
                    <span class="mt-px w-5 shrink-0 text-[0.6875rem] font-semibold tabular-nums text-slate-400">01</span>
                    <p class="text-[0.8125rem] leading-5 text-slate-600">
                        Abre el enlace antes de <span class="font-medium text-slate-900">60 minutos</span>.
                    </p>
                </div>

                <div class="flex gap-3 py-3">
                    <span class="mt-px w-5 shrink-0 text-[0.6875rem] font-semibold tabular-nums text-slate-400">02</span>
                    <p class="text-[0.8125rem] leading-5 text-slate-600">
                        Crea una contraseña nueva de al menos 8 caracteres, con letras y números.
                    </p>
                </div>

                <div class="flex gap-3 py-3">
                    <span class="mt-px w-5 shrink-0 text-[0.6875rem] font-semibold tabular-nums text-slate-400">03</span>
                    <p class="text-[0.8125rem] leading-5 text-slate-600">
                        Inicia sesión otra vez. Tu cuenta y tus proyectos siguen intactos.
                    </p>
                </div>
            </div>

            @if ($email)
                <div class="mt-6 rounded-lg border border-slate-200/80 bg-slate-50/60 p-3.5">
                    <p class="text-[0.75rem] font-semibold text-slate-800">¿No te llegó?</p>
                    <p class="mt-1 text-[0.75rem] leading-5 text-slate-500">
                        Revisa las carpetas de spam o promociones. Si el correo está mal escrito, puedes corregirlo.
                    </p>

                    <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2">
                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <input type="hidden" name="email" value="{{ $email }}">

                            <button
                                type="submit"
                                class="rounded-sm text-[0.75rem] font-medium text-blue-700 underline-offset-4 transition-colors duration-150 hover:text-blue-800 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2"
                            >
                                Reenviar el enlace
                            </button>
                        </form>

                        <a
                            href="{{ route('password.request') }}"
                            class="rounded-sm text-[0.75rem] font-medium text-slate-500 underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2"
                        >
                            Usar otro correo
                        </a>
                    </div>
                </div>
            @endif

            <a
                href="{{ route('login') }}"
                class="mt-6 inline-flex h-11 w-full items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-4 text-[0.875rem] font-medium text-slate-700 shadow-[0_1px_1px_rgb(15_23_42/0.03)] outline-none transition-[background-color,border-color,transform] duration-150 ease-out hover:border-slate-300 hover:bg-slate-50 focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 motion-safe:active:scale-[0.99]"
            >
                Volver a iniciar sesión
            </a>
        @else
            <div>
                <p class="text-[0.6875rem] font-semibold tracking-[0.16em] text-blue-700 uppercase">Recuperación</p>

                <h1 class="mt-2.5 text-[1.625rem] leading-[1.2] font-semibold tracking-[-0.02em] text-slate-900 sm:text-[1.75rem]">
                    ¿Olvidaste tu contraseña?
                </h1>

                <p class="mt-2 text-[0.875rem] leading-6 text-slate-600">
                    Escribe el correo de tu cuenta y te enviaremos un enlace para crear una contraseña nueva.
                </p>
            </div>

            <form
                method="POST"
                action="{{ route('password.email') }}"
                class="mt-7 space-y-4"
                x-data="{ submitting: false }"
                @submit="submitting = true"
                :aria-busy="submitting"
                novalidate
            >
                @csrf

                <x-ui.input
                    name="email"
                    label="Correo electrónico"
                    type="email"
                    icon="mail"
                    autocomplete="username"
                    inputmode="email"
                    autocapitalize="none"
                    spellcheck="false"
                    placeholder="nombre.apellido@correo.com"
                    hint="Usa el mismo correo con el que te registraste."
                    maxlength="255"
                    required
                    autofocus
                />

                <div class="pt-1">
                    <x-ui.submit-button label="Enviar enlace" loading-label="Enviando…" />
                </div>
            </form>

            <div class="mt-6 flex items-center gap-2.5 border-t border-slate-200/80 pt-5 text-[0.75rem] leading-5 text-slate-500">
                <x-ui.icon name="information-circle" class="h-4 w-4 shrink-0 text-slate-400" />
                <p>Si no ves el mensaje en unos minutos, revisa la carpeta de spam.</p>
            </div>
        @endif
    </div>

    <p class="mt-5 px-1 text-center text-[0.75rem] leading-5 text-slate-500 sm:mt-6">
        @if ($sent)
            Si no solicitaste este cambio, ignora el mensaje: tu contraseña sigue igual.
        @else
            ¿No tienes cuenta todavía?
            <a
                href="{{ route('register') }}"
                class="rounded-sm font-medium text-slate-700 underline-offset-4 transition-colors duration-150 hover:text-blue-700 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2"
            >
                Crear cuenta
            </a>
        @endif
    </p>
@endsection
