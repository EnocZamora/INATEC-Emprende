@extends('layouts.auth')

@section('title', 'Iniciar sesión')
@section('description', 'Accede con tu cuenta institucional para publicar y administrar tus proyectos en INATEC Emprende.')

@section('aside-eyebrow', 'Acceso institucional')
@section('aside-heading', 'Tu proyecto técnico, visible para todo el país.')
@section('aside-copy', 'Publica lo que construyes en tu centro, recibe visitas y deja que empresas y comunidad te contacten directamente por WhatsApp.')

@section('aside-points')
    <x-auth.point index="01" title="Catálogo sin promoción pagada">
        Los proyectos se ordenan por fecha, carrera y centro. Ningún pago altera el orden.
    </x-auth.point>

    <x-auth.point index="02" title="Contacto directo">
        Quien visita tu proyecto te escribe por WhatsApp, sin intermediarios ni comisiones.
    </x-auth.point>

    <x-auth.point index="03" title="Tu proyecto, tu cuenta">
        Solo tú puedes editar, pausar o retirar lo que publicas.
    </x-auth.point>
@endsection

@section('content')
    <div class="sm:rounded-2xl sm:border sm:border-slate-200/80 sm:bg-white sm:p-8 sm:shadow-[0_1px_2px_rgb(15_23_42/0.04),0_12px_32px_-24px_rgb(15_23_42/0.18)]">
        <div>
            <p class="text-[0.6875rem] font-semibold tracking-[0.16em] text-blue-700 uppercase">Acceso</p>

            <h1 class="mt-2.5 text-[1.625rem] leading-[1.2] font-semibold tracking-[-0.02em] text-slate-900 sm:text-[1.75rem]">
                Inicia sesión
            </h1>

            <p class="mt-2 text-[0.875rem] leading-6 text-slate-600">
                Usa el correo con el que creaste tu cuenta de estudiante.
            </p>
        </div>

        @if (session('status'))
            <x-ui.alert variant="success" class="mt-6">{{ session('status') }}</x-ui.alert>
        @endif

        <form
            method="POST"
            action="{{ route('login') }}"
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
                required
                autofocus
            />

            <x-ui.password-input
                name="password"
                label="Contraseña"
                autocomplete="current-password"
                placeholder="Tu contraseña"
                required
            >
                <x-slot:action>
                    <a
                        href="{{ route('password.request') }}"
                        class="rounded-sm text-[0.75rem] font-medium text-slate-500 underline-offset-4 transition-colors duration-150 hover:text-blue-700 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2"
                    >
                        ¿Olvidaste tu contraseña?
                    </a>
                </x-slot:action>
            </x-ui.password-input>

            <div class="pt-0.5">
                <x-ui.checkbox name="remember">Mantener la sesión iniciada</x-ui.checkbox>
            </div>

            <div class="pt-1">
                <x-ui.submit-button label="Iniciar sesión" loading-label="Verificando…" />
            </div>
        </form>

        <div class="mt-6 border-t border-slate-200/80 pt-5 text-center text-[0.8125rem] text-slate-600">
            ¿Aún no tienes cuenta?
            <a
                href="{{ route('register') }}"
                class="rounded-sm font-medium text-blue-700 underline-offset-4 transition-colors duration-150 hover:text-blue-800 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2"
            >
                Crear cuenta
            </a>
        </div>
    </div>

    <p class="mt-5 px-1 text-center text-[0.75rem] leading-5 text-slate-500 sm:mt-6">
        El acceso es exclusivo para estudiantes y personal de INATEC.
    </p>
@endsection
