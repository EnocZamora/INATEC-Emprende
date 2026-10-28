<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, interactive-widget=resizes-content">
    <meta name="color-scheme" content="light">
    <meta name="theme-color" content="#ffffff">
    <meta name="description" content="@yield('description', 'Acceso al portal de proyectos estudiantiles de INATEC Emprende.')">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo-lightbulb.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon-180.png') }}">

    <title>@yield('title', 'Acceso') · INATEC Emprende</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-dvh bg-white font-sans text-slate-900 antialiased">
    <a href="#contenido" class="sr-only focus:not-sr-only focus:fixed focus:start-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-slate-900 focus:px-4 focus:py-2 focus:text-sm focus:font-medium focus:text-white">
        Saltar al contenido
    </a>

    <div class="lg:grid lg:min-h-dvh lg:grid-cols-[1.06fr_1fr]">
        {{-- Institutional context. Static, quietly informative, never a marketing pitch. --}}
        <aside class="hidden border-e border-slate-200/80 bg-slate-50 lg:sticky lg:top-0 lg:flex lg:h-dvh lg:flex-col lg:justify-between lg:overflow-y-auto lg:px-12 lg:py-10 xl:px-16 xl:py-12">
            <div class="flex items-center justify-between gap-6">
                <x-brand.wordmark />

                <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full border border-slate-200 bg-white px-2.5 py-1 text-[0.625rem] font-semibold tracking-[0.1em] text-slate-500 uppercase">
                    <span class="h-1.5 w-1.5 rounded-full bg-blue-600" aria-hidden="true"></span>
                    Institucional
                </span>
            </div>

            <div class="max-w-[32rem] py-10">
                <p class="text-[0.6875rem] font-semibold tracking-[0.16em] text-blue-700 uppercase">
                    @yield('aside-eyebrow', 'Portal institucional')
                </p>

                <p class="mt-4 text-[2.25rem] leading-[1.12] font-semibold tracking-[-0.025em] text-balance text-slate-900 xl:text-[2.625rem]">
                    @yield('aside-heading', 'Proyectos estudiantiles, en un solo lugar.')
                </p>

                <p class="mt-4 max-w-[30rem] text-[0.9375rem] leading-6 text-slate-600">
                    @yield('aside-copy')
                </p>

                <div class="mt-8 divide-y divide-slate-200/80 border-t border-slate-200/80">
                    @yield('aside-points')
                </div>
            </div>

            <div class="flex items-center justify-between gap-6 border-t border-slate-200/80 pt-5 text-[0.6875rem] text-slate-500">
                <p>INATEC · Tecnológico Nacional</p>

                <nav class="flex gap-4" aria-label="Información legal">
                    <a href="{{ route('terms') }}" class="rounded-sm transition-colors duration-150 hover:text-slate-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2">Términos</a>
                    <a href="{{ route('privacy') }}" class="rounded-sm transition-colors duration-150 hover:text-slate-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2">Privacidad</a>
                </nav>
            </div>
        </aside>

        <main id="contenido" class="flex min-h-dvh min-w-0 flex-col">
            <header class="flex items-center justify-between gap-4 border-b border-slate-200/80 px-5 pt-5 pb-4 sm:px-8 lg:hidden">
                <x-brand.wordmark compact />

                <a href="{{ route('catalog.index') }}" class="inline-flex items-center gap-1.5 rounded-md text-[0.75rem] font-medium text-slate-500 transition-colors duration-150 hover:text-slate-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2">
                    <x-ui.icon name="arrow-left" class="h-3.5 w-3.5" />
                    Catálogo
                </a>
            </header>

            <div class="hidden justify-end px-12 pt-9 lg:flex xl:px-14">
                <a href="{{ route('catalog.index') }}" class="group inline-flex items-center gap-1.5 rounded-md text-[0.8125rem] font-medium text-slate-500 transition-colors duration-150 hover:text-slate-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2">
                    <x-ui.icon name="arrow-left" class="h-4 w-4 transition-transform duration-150 group-hover:-translate-x-0.5 motion-reduce:transition-none motion-reduce:group-hover:translate-x-0" />
                    Volver al catálogo
                </a>
            </div>

            <div class="flex flex-1 justify-center px-5 pt-8 pb-6 sm:px-8 sm:pt-10 lg:px-12 lg:py-8">
                <div class="w-full max-w-[27rem] lg:my-auto">
                    @yield('content')

                    <p class="mt-7 hidden items-center justify-center gap-1.5 text-[0.6875rem] text-slate-400 lg:flex">
                        <x-ui.icon name="shield-check" class="h-3.5 w-3.5" />
                        Conexión segura · Solo cuentas institucionales
                    </p>
                </div>
            </div>

            <footer class="flex items-center justify-between gap-4 px-5 pt-4 pb-[max(1.25rem,env(safe-area-inset-bottom))] text-[0.6875rem] text-slate-500 sm:px-8 lg:hidden">
                <p>INATEC · Tecnológico Nacional</p>

                <nav class="flex gap-4" aria-label="Información legal">
                    <a href="{{ route('terms') }}" class="transition-colors duration-150 hover:text-slate-900">Términos</a>
                    <a href="{{ route('privacy') }}" class="transition-colors duration-150 hover:text-slate-900">Privacidad</a>
                </nav>
            </footer>
        </main>
    </div>
</body>
</html>
