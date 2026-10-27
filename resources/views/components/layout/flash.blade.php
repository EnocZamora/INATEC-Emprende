@php
    /*
     | Controllers flash short codes for project actions and full sentences for
     | auth flows. Codes are translated here so copy can change without touching
     | PHP; anything unknown falls through verbatim.
     */
    $status = session('status');

    $messages = [
        'project-created' => [
            'title' => 'Emprendimiento creado',
            'body' => 'Ya aparece en tu panel. Revísalo y publícalo cuando esté listo.',
        ],
        'project-updated' => [
            'title' => 'Cambios guardados',
            'body' => 'La información pública del emprendimiento ya está actualizada.',
        ],
        'project-deleted' => [
            'title' => 'Emprendimiento eliminado',
            'body' => 'Ya no aparece en el catálogo ni en tu panel.',
        ],
        'profile-updated' => [
            'title' => 'Perfil actualizado',
            'body' => 'Tus datos de contacto ya están vigentes en los emprendimientos que publicaste.',
        ],
        'password-updated' => [
            'title' => 'Contraseña actualizada',
            'body' => 'Usa la nueva contraseña la próxima vez que inicies sesión.',
        ],
    ];

    $flash = filled($status)
        ? ($messages[$status] ?? ['title' => null, 'body' => (string) $status])
        : null;
@endphp

@if ($flash)
    <div
        x-data="{ visible: true }"
        x-show="visible"
        class="mx-auto w-full max-w-6xl px-5 pt-6 sm:px-8"
    >
        <x-ui.alert variant="success">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    @if ($flash['title'])
                        <p class="font-semibold">{{ $flash['title'] }}</p>
                    @endif

                    <p @class(['mt-0.5' => $flash['title']])>{{ $flash['body'] }}</p>
                </div>

                <button
                    type="button"
                    @click="visible = false"
                    class="-me-1 -mt-1 flex h-7 w-7 shrink-0 items-center justify-center rounded-md text-emerald-700/60 transition-colors duration-150 hover:bg-emerald-100/60 hover:text-emerald-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600/40 motion-safe:active:scale-95"
                    aria-label="Cerrar aviso"
                >
                    <x-ui.icon name="x" class="h-3.5 w-3.5" />
                </button>
            </div>
        </x-ui.alert>
    </div>
@endif
