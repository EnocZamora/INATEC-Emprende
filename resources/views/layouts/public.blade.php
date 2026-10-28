<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, interactive-widget=resizes-content">
    <meta name="color-scheme" content="light">
    <meta name="theme-color" content="#ffffff">
    <meta name="description" content="@yield('description', 'Catálogo institucional de emprendimientos técnicos de egresados certificados por INATEC. Contacto directo, sin promoción pagada.')">

    <title>@yield('title', 'Emprendimientos técnicos') · INATEC Emprende</title>

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="INATEC Emprende">
    <meta property="og:title" content="@yield('title', 'Emprendimientos técnicos') · INATEC Emprende">
    <meta property="og:description" content="@yield('description', 'Catálogo institucional de emprendimientos técnicos de egresados certificados por INATEC.')">
    <meta property="og:url" content="{{ url()->current() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-dvh flex-col bg-white font-sans text-slate-900 antialiased">
    <a
        href="#contenido"
        class="sr-only focus:not-sr-only focus:fixed focus:start-4 focus:top-4 focus:z-[60] focus:rounded-lg focus:bg-slate-900 focus:px-4 focus:py-2 focus:text-sm focus:font-medium focus:text-white"
    >
        Saltar al contenido
    </a>

    @php
        $user = auth()->user();

        $catalogActive = request()->routeIs('catalog.index', 'projects.show');
        $searchActive = request()->routeIs('catalog.search');
        $navLink = 'inline-flex items-center rounded-full px-3 py-1.5 text-[0.8125rem] font-medium outline-none transition-colors duration-150 focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2';
    @endphp

    <header x-data="mobileNav" class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/85 backdrop-blur-md supports-[backdrop-filter]:bg-white/75">
        <div class="mx-auto flex h-16 max-w-6xl items-center gap-3 px-5 sm:px-8">
            <x-brand.wordmark class="me-auto" />

            <nav class="hidden items-center gap-0.5 lg:flex" aria-label="Navegación principal">
                <a
                    href="{{ route('catalog.index') }}"
                    @class([$navLink, 'bg-blue-50 text-blue-800' => $catalogActive, 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' => ! $catalogActive])
                    @if ($catalogActive) aria-current="page" @endif
                >
                    Catálogo
                </a>

                <a
                    href="{{ route('catalog.search') }}"
                    @class([$navLink, 'bg-blue-50 text-blue-800' => $searchActive, 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' => ! $searchActive])
                    @if ($searchActive) aria-current="page" @endif
                >
                    Buscar
                </a>

                <a href="{{ route('home') }}#como-funciona" class="{{ $navLink }} text-slate-500 hover:bg-slate-50 hover:text-slate-900">
                    Cómo funciona
                </a>
            </nav>

            <div class="ms-auto flex items-center gap-2 lg:ms-0">
                <a
                    href="{{ route('catalog.search') }}"
                    class="hidden h-9 w-9 items-center justify-center rounded-lg text-slate-500 outline-none transition-colors duration-150 hover:bg-slate-100 hover:text-slate-900 focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2 sm:inline-flex"
                    aria-label="Buscar emprendimientos"
                >
                    <x-ui.icon name="search" class="h-[1.125rem] w-[1.125rem]" />
                </a>

                @guest
                    <a
                        href="{{ route('login') }}"
                        class="hidden rounded-lg px-3 py-2 text-[0.8125rem] font-medium text-slate-600 outline-none transition-colors duration-150 hover:bg-slate-100 hover:text-slate-900 focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2 sm:inline-flex"
                    >
                        Iniciar sesión
                    </a>

                    {{-- Wrapper: the button component's own `inline-flex` would beat a `hidden` utility. --}}
                    <span class="hidden sm:block">
                        <x-ui.button href="{{ route('register') }}" size="sm" icon="plus">
                            Publicar emprendimiento
                        </x-ui.button>
                    </span>
                @else
                    <span class="hidden sm:block">
                        <x-ui.button href="{{ route('projects.create') }}" size="sm" icon="plus">
                            Nuevo emprendimiento
                        </x-ui.button>
                    </span>

                    <div class="relative" x-data="userMenu" @keydown.escape.window="close()" @click.outside="close()">
                        <button
                            type="button"
                            @click="toggle()"
                            :aria-expanded="open"
                            aria-haspopup="menu"
                            class="flex items-center gap-2 rounded-full border border-slate-200/90 bg-white py-1 ps-1 pe-2 outline-none transition-[border-color,background-color] duration-150 hover:border-slate-300 hover:bg-slate-50 focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2 motion-safe:active:scale-[0.98]"
                        >
                            <x-ui.avatar :name="$user->name" :src="$user->avatarUrl()" size="sm" />

                            <span class="hidden max-w-24 truncate text-[0.8125rem] font-medium text-slate-700 sm:block">
                                {{ $user->name }}
                            </span>

                            <x-ui.icon name="chevron-down" class="h-3.5 w-3.5 text-slate-400 transition-transform duration-150" x-bind:class="open ? 'rotate-180' : ''" />
                        </button>

                        <div
                            x-show="open"
                            x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 -translate-y-0.5 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 -translate-y-0.5 scale-95"
                            class="absolute end-0 z-50 mt-2 w-64 origin-top-right rounded-xl border border-slate-200/90 bg-white p-1.5 shadow-[0_1px_2px_rgb(15_23_42/0.04),0_18px_40px_-16px_rgb(15_23_42/0.22)]"
                            role="menu"
                        >
                            <div class="px-2.5 py-2">
                                <p class="truncate text-[0.8125rem] font-semibold text-slate-900">{{ $user->name }}</p>
                                <p class="mt-0.5 truncate text-[0.6875rem] text-slate-500">{{ $user->email }}</p>

                                @if ($user->isAdmin())
                                    <x-ui.badge variant="blue" class="mt-2">Administrador</x-ui.badge>
                                @endif
                            </div>

                            <div class="my-1 h-px bg-slate-100"></div>

                            <a href="{{ route('dashboard') }}" role="menuitem" class="flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-[0.8125rem] font-medium text-slate-700 outline-none transition-colors duration-150 hover:bg-slate-50 hover:text-slate-900 focus-visible:bg-slate-50">
                                <x-ui.icon name="grid" class="h-4 w-4 text-slate-400" />
                                Mi panel
                            </a>

                            <a href="{{ route('profile.show') }}" role="menuitem" class="flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-[0.8125rem] font-medium text-slate-700 outline-none transition-colors duration-150 hover:bg-slate-50 hover:text-slate-900 focus-visible:bg-slate-50">
                                <x-ui.icon name="user" class="h-4 w-4 text-slate-400" />
                                Mi perfil
                            </a>

                            <a href="{{ route('projects.create') }}" role="menuitem" class="flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-[0.8125rem] font-medium text-slate-700 outline-none transition-colors duration-150 hover:bg-slate-50 hover:text-slate-900 focus-visible:bg-slate-50">
                                <x-ui.icon name="plus" class="h-4 w-4 text-slate-400" />
                                Nuevo emprendimiento
                            </a>

                            <a href="{{ route('catalog.index') }}" role="menuitem" class="flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-[0.8125rem] font-medium text-slate-700 outline-none transition-colors duration-150 hover:bg-slate-50 hover:text-slate-900 focus-visible:bg-slate-50">
                                <x-ui.icon name="grid" class="h-4 w-4 text-slate-400" />
                                Catálogo público
                            </a>

                            <div class="my-1 h-px bg-slate-100"></div>

                            <form method="POST" action="{{ route('logout') }}" role="none">
                                @csrf
                                <button type="submit" role="menuitem" class="flex w-full items-center gap-2.5 rounded-lg px-2.5 py-2 text-[0.8125rem] font-medium text-slate-600 outline-none transition-colors duration-150 hover:bg-slate-50 hover:text-slate-900 focus-visible:bg-slate-50">
                                    <x-ui.icon name="logout" class="h-4 w-4 text-slate-400" />
                                    Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                @endguest

                <button
                    type="button"
                    @click="toggle()"
                    :aria-expanded="open"
                    aria-controls="menu-movil"
                    class="-me-1 inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-600 outline-none transition-colors duration-150 hover:bg-slate-100 hover:text-slate-900 focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2 lg:hidden"
                >
                    <x-ui.icon name="menu" class="h-5 w-5" x-show="!open" />
                    <x-ui.icon name="x" class="h-5 w-5" x-show="open" x-cloak />
                    <span class="sr-only">Abrir menú</span>
                </button>
            </div>
        </div>

        <div
            id="menu-movil"
            x-show="open"
            x-cloak
            x-transition.opacity.duration.150ms
            class="absolute inset-x-0 top-full border-b border-slate-200/80 bg-white shadow-[0_18px_40px_-24px_rgb(15_23_42/0.25)] lg:hidden"
        >
            <div class="mx-auto max-h-[calc(100dvh-4rem)] max-w-6xl overflow-y-auto px-5 py-4 pb-[max(1.25rem,env(safe-area-inset-bottom))] sm:px-8">
                <nav class="space-y-0.5" aria-label="Navegación móvil">
                    <a href="{{ route('catalog.index') }}" @click="close()" class="flex items-center justify-between rounded-lg px-3 py-2.5 text-[0.875rem] font-medium text-slate-700 transition-colors duration-150 hover:bg-slate-50 hover:text-slate-900">
                        Catálogo
                        <x-ui.icon name="chevron-right" class="h-4 w-4 text-slate-300" />
                    </a>

                    <a href="{{ route('catalog.search') }}" @click="close()" class="flex items-center justify-between rounded-lg px-3 py-2.5 text-[0.875rem] font-medium text-slate-700 transition-colors duration-150 hover:bg-slate-50 hover:text-slate-900">
                        Buscar emprendimientos
                        <x-ui.icon name="chevron-right" class="h-4 w-4 text-slate-300" />
                    </a>

                    <a href="{{ route('home') }}#como-funciona" @click="close()" class="flex items-center justify-between rounded-lg px-3 py-2.5 text-[0.875rem] font-medium text-slate-700 transition-colors duration-150 hover:bg-slate-50 hover:text-slate-900">
                        Cómo funciona
                        <x-ui.icon name="chevron-right" class="h-4 w-4 text-slate-300" />
                    </a>
                </nav>

                <div class="my-3 h-px bg-slate-100"></div>

                @guest
                    <div class="space-y-2">
                        <x-ui.button href="{{ route('register') }}" icon="plus" class="w-full">
                            Publicar mi emprendimiento
                        </x-ui.button>

                        <x-ui.button href="{{ route('login') }}" variant="secondary" class="w-full">
                            Iniciar sesión
                        </x-ui.button>
                    </div>
                @else
                    <div class="space-y-2">
                        <x-ui.button href="{{ route('dashboard') }}" icon="grid" class="w-full">
                            Mi panel
                        </x-ui.button>

                        <x-ui.button href="{{ route('profile.show') }}" variant="secondary" icon="user" class="w-full">
                            Mi perfil
                        </x-ui.button>

                        <x-ui.button href="{{ route('projects.create') }}" variant="secondary" icon="plus" class="w-full">
                            Nuevo emprendimiento
                        </x-ui.button>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-[0.8125rem] font-medium text-slate-500 transition-colors duration-150 hover:bg-slate-50 hover:text-slate-900">
                                <x-ui.icon name="logout" class="h-4 w-4" />
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                @endguest
            </div>
        </div>
    </header>

    <main id="contenido" class="flex-1">
        <x-layout.flash />

        @yield('content')
    </main>

    <footer class="border-t border-slate-200/80 bg-white">
        <div class="mx-auto max-w-6xl px-5 py-12 sm:px-8">
            <div class="grid gap-10 md:grid-cols-[1.5fr_1fr_1fr_1fr]">
                <div class="max-w-xs">
                    <x-brand.wordmark />

                    <p class="mt-4 text-[0.8125rem] leading-6 text-slate-500">
                        El escaparate de los emprendimientos técnicos de los egresados certificados por INATEC. Sin promoción pagada, con contacto directo entre emprendedores y comunidad.
                    </p>

                    <p class="mt-4 inline-flex items-center gap-1.5 text-[0.6875rem] font-medium text-slate-500">
                        <x-ui.icon name="shield-check" class="h-3.5 w-3.5 text-blue-700" />
                        Egresados certificados por INATEC
                    </p>
                </div>

                <div>
                    <p class="text-[0.6875rem] font-semibold tracking-[0.14em] text-slate-400 uppercase">Explorar</p>

                    <ul class="mt-3 space-y-2 text-[0.8125rem]">
                        <li><a href="{{ route('catalog.index') }}" class="rounded-sm text-slate-600 underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline">Catálogo completo</a></li>
                        <li><a href="{{ route('catalog.search') }}" class="rounded-sm text-slate-600 underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline">Buscar emprendimientos</a></li>
                        <li><a href="{{ route('home') }}#como-funciona" class="rounded-sm text-slate-600 underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline">Cómo funciona</a></li>
                    </ul>
                </div>

                <div>
                    <p class="text-[0.6875rem] font-semibold tracking-[0.14em] text-slate-400 uppercase">Especialidades</p>

                    <ul class="mt-3 space-y-2 text-[0.8125rem]">
                        @foreach (array_slice(\App\Http\Requests\ProjectStoreRequest::CATEGORIES, 0, 4) as $category)
                            <li>
                                <a href="{{ route('catalog.index', ['category' => $category]) }}" class="rounded-sm text-slate-600 underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline">
                                    {{ $category }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <p class="text-[0.6875rem] font-semibold tracking-[0.14em] text-slate-400 uppercase">Cuenta</p>

                    <ul class="mt-3 space-y-2 text-[0.8125rem]">
                        @auth
                            <li><a href="{{ route('dashboard') }}" class="rounded-sm text-slate-600 underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline">Mi panel</a></li>
                            <li><a href="{{ route('profile.show') }}" class="rounded-sm text-slate-600 underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline">Mi perfil</a></li>
                            <li><a href="{{ route('projects.create') }}" class="rounded-sm text-slate-600 underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline">Nuevo emprendimiento</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="rounded-sm text-slate-600 underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline">Iniciar sesión</a></li>
                            <li><a href="{{ route('register') }}" class="rounded-sm text-slate-600 underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline">Crear cuenta</a></li>
                        @endauth
                        <li><a href="{{ route('terms') }}" class="rounded-sm text-slate-600 underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline">Términos de uso</a></li>
                        <li><a href="{{ route('privacy') }}" class="rounded-sm text-slate-600 underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline">Privacidad</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-10 flex flex-col gap-3 border-t border-slate-200/80 pt-6 text-[0.6875rem] text-slate-500 sm:flex-row sm:items-center sm:justify-between">
                <p>© {{ date('Y') }} INATEC · Tecnológico Nacional. Todos los derechos reservados.</p>

                <p class="flex items-center gap-1.5">
                    <x-ui.icon name="globe" class="h-3.5 w-3.5 text-slate-400" />
                    Hecho en Nicaragua · Sin rastreadores publicitarios
                </p>
            </div>
        </div>
    </footer>
</body>
</html>
