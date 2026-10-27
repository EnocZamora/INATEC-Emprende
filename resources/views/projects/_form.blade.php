@php
    $project = $project ?? null;
    $editing = $project !== null;
    $action = $editing ? route('projects.update', $project) : route('projects.store');

    $cover = $editing && filled($project->cover_photo)
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($project->cover_photo)
        : null;

    // A suspended project cannot be saved back as suspended by a student, so
    // the form opens with no state selected and asks for a new decision.
    $currentStatus = old('status', $editing ? ($project->isSuspended() ? '' : $project->status) : 'draft');

    $isAdmin = auth()->user()->isAdmin();

    $statusOptions = [
        'draft' => [
            'label' => 'Borrador',
            'description' => 'Solo tú lo ves. Puedes publicarlo cuando esté listo.',
        ],
        'published' => [
            'label' => 'Publicado',
            'description' => 'Aparece en el catálogo y cualquier visitante puede verlo.',
        ],
    ];

    if ($isAdmin) {
        $statusOptions['suspended'] = [
            'label' => 'Suspendido',
            'description' => 'Se retira del catálogo público para revisión.',
        ];
    }
@endphp

<form
    method="POST"
    action="{{ $action }}"
    enctype="multipart/form-data"
    x-data="{ submitting: false }"
    @submit="submitting = true"
    :aria-busy="submitting"
    class="scroll-mt-24"
    novalidate
>
    @csrf

    @if ($editing)
        @method('PUT')
    @endif

    @if ($errors->any())
        <x-ui.alert variant="error" title="Revisa la información" class="mb-6">
            <ul class="mt-1 list-disc space-y-0.5 ps-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-ui.alert>
    @endif

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_18rem] lg:items-start">
        <div class="min-w-0 space-y-6">
            <section class="rounded-xl border border-slate-200/80 bg-white p-5 sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-[0.9375rem] font-semibold tracking-[-0.01em] text-slate-900">Información del proyecto</h2>
                        <p class="mt-1 text-[0.75rem] leading-5 text-slate-500">
                            Es lo primero que verá quien visite el catálogo.
                        </p>
                    </div>

                    <x-ui.badge variant="neutral">Obligatorio</x-ui.badge>
                </div>

                <div class="mt-5 space-y-5">
                    <x-ui.input
                        name="title"
                        label="Título del proyecto"
                        :value="old('title', $project?->title)"
                        maxlength="150"
                        placeholder="Ej. Sistema de riego automatizado para huertos escolares"
                        hint="Sé específico: un título claro se entiende mejor que un eslogan."
                        required
                    />

                    <x-ui.select name="category" label="Especialidad">
                        <option value="">Selecciona una categoría</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category }}" @selected(old('category', $project?->category) === $category)>
                                {{ $category }}
                            </option>
                        @endforeach
                    </x-ui.select>

                    <x-ui.textarea
                        name="description"
                        label="Descripción"
                        :value="old('description', $project?->description)"
                        rows="9"
                        maxlength="10000"
                        placeholder="Cuenta qué problema resuelve el proyecto, cómo funciona y en qué estado se encuentra."
                        hint="Incluye el problema, la solución y el estado actual. El texto conserva los saltos de línea."
                        required
                    />
                </div>
            </section>

            <section class="rounded-xl border border-slate-200/80 bg-white p-5 sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-[0.9375rem] font-semibold tracking-[-0.01em] text-slate-900">Portada</h2>
                        <p class="mt-1 text-[0.75rem] leading-5 text-slate-500">
                            La imagen que representa el proyecto en el catálogo y al compartir el enlace.
                        </p>
                    </div>

                    <x-ui.badge variant="neutral">JPG · PNG · WebP</x-ui.badge>
                </div>

                <div
                    class="mt-5"
                    x-data="coverField({ current: @js($cover) })"
                >
                    <label for="cover_photo" class="text-[0.8125rem] font-medium text-slate-700">Foto de portada</label>

                    <div class="mt-1.5 overflow-hidden rounded-xl border border-dashed border-slate-300 bg-slate-50/60">
                        <div class="relative" :class="preview ? 'aspect-[16/10]' : 'h-56 sm:h-64'">
                            <template x-if="preview">
                                <img :src="preview" alt="Vista previa de la portada" class="absolute inset-0 h-full w-full object-cover">
                            </template>

                            <div x-show="! preview" class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-slate-400">
                                <x-ui.icon name="photo" class="h-7 w-7" />
                                <p class="text-[0.75rem]">Sin portada seleccionada</p>
                                <p class="text-[0.6875rem] text-slate-400">Horizontal, 16:10 · máximo 2 MB</p>
                            </div>

                            <div class="absolute inset-x-0 bottom-0 flex items-center justify-between gap-3 bg-slate-900/55 px-3 py-2 backdrop-blur-sm">
                                <p class="truncate text-[0.6875rem] text-white/90" x-text="filename ?? '{{ $editing && $cover ? 'Portada actual' : 'Ningún archivo' }}'"></p>

                                <p class="shrink-0 text-[0.6875rem] tabular-nums text-white/70" x-show="filesize" x-text="filesize"></p>

                                <button
                                    type="button"
                                    @click="$refs.cover.click()"
                                    class="shrink-0 rounded-md bg-white/15 px-2 py-1 text-[0.6875rem] font-medium text-white outline-none transition-colors duration-150 hover:bg-white/25 focus-visible:ring-2 focus-visible:ring-white/60"
                                >
                                    {{ $editing && $cover ? 'Cambiar' : 'Elegir imagen' }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <input
                        x-ref="cover"
                        id="cover_photo"
                        name="cover_photo"
                        type="file"
                        accept="image/jpeg,image/png,image/webp"
                        class="sr-only"
                        @change="choose($event)"
                    >

                    <div class="mt-2 flex flex-wrap items-center justify-between gap-2">
                        <p class="text-[0.6875rem] leading-5 text-slate-500" x-show="filename" x-cloak>
                            Nueva imagen lista para guardar.
                        </p>

                        <button
                            type="button"
                            x-show="filename"
                            x-cloak
                            @click="clear()"
                            class="text-[0.6875rem] font-medium text-slate-500 underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline"
                        >
                            Quitar selección
                        </button>
                    </div>

                    <p class="mt-1 text-[0.6875rem] leading-5 text-red-600" x-show="error" x-text="error"></p>

                    @error('cover_photo')
                        <p class="mt-1.5 text-[0.75rem] leading-5 text-red-600">{{ $message }}</p>
                    @enderror

                    @if ($editing && $cover && ! $errors->has('cover_photo'))
                        <p class="mt-1.5 text-[0.6875rem] leading-5 text-slate-500">
                            Si no eliges otra imagen, se conserva la portada actual.
                        </p>
                    @endif
                </div>
            </section>

            <section class="rounded-xl border border-slate-200/80 bg-white p-5 sm:p-6">
                <h2 class="text-[0.9375rem] font-semibold tracking-[-0.01em] text-slate-900">Estado de publicación</h2>
                <p class="mt-1 text-[0.75rem] leading-5 text-slate-500">
                    Publicar no cuesta nada y no altera la posición del proyecto en el catálogo.
                </p>

                <div class="mt-4 grid gap-2 sm:grid-cols-2">
                    @foreach ($statusOptions as $value => $option)
                        <label class="group relative flex cursor-pointer items-start gap-3 rounded-lg border border-slate-200 bg-white p-3.5 transition-[border-color,background-color,box-shadow] duration-150 hover:border-slate-300 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/40 has-[:checked]:ring-1 has-[:checked]:ring-blue-600">
                            <input
                                type="radio"
                                name="status"
                                value="{{ $value }}"
                                @checked($currentStatus === $value)
                                class="mt-0.5 h-4 w-4 shrink-0 accent-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2"
                            >

                            <span class="min-w-0">
                                <span class="block text-[0.8125rem] font-semibold text-slate-900">{{ $option['label'] }}</span>
                                <span class="mt-0.5 block text-[0.6875rem] leading-5 text-slate-500">{{ $option['description'] }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>

                @error('status')
                    <p class="mt-2 text-[0.75rem] leading-5 text-red-600">{{ $message }}</p>
                @enderror

                @if ($editing && $project->isSuspended() && ! $isAdmin)
                    <p class="mt-3 rounded-md border border-amber-200 bg-amber-50/60 px-3 py-2 text-[0.6875rem] leading-5 text-amber-800">
                        Este proyecto está suspendido. Elige «Borrador» o «Publicado» para guardar los cambios.
                    </p>
                @endif
            </section>

            <div class="flex flex-col-reverse gap-2 border-t border-slate-200/80 pt-5 sm:flex-row sm:items-center sm:justify-end">
                <x-ui.button href="{{ $editing ? route('projects.show', $project) : route('dashboard') }}" variant="ghost">
                    Cancelar
                </x-ui.button>

                <x-ui.submit-button
                    :label="$editing ? 'Guardar cambios' : 'Crear proyecto'"
                    :loading-label="$editing ? 'Guardando…' : 'Creando…'"
                    class="sm:w-auto sm:min-w-44"
                />
            </div>
        </div>

        <aside class="space-y-4" aria-label="Consejos para publicar">
            @if ($editing)
                <div class="rounded-xl border border-slate-200/80 bg-white p-5">
                    <p class="text-[0.625rem] font-semibold tracking-[0.14em] text-slate-500 uppercase">Estado actual</p>

                    <div class="mt-3">
                        <x-status-badge :status="$project->status" />
                    </div>

                    <p class="mt-3 text-[0.6875rem] leading-5 text-slate-500">
                        Los cambios de estado se aplican en cuanto guardas. Puedes ver la página pública antes de publicarla.
                    </p>

                    <x-ui.button href="{{ route('projects.show', $project) }}" variant="secondary" size="sm" icon="eye" class="mt-3 w-full">
                        Ver proyecto
                    </x-ui.button>
                </div>
            @endif

            <div class="rounded-xl border border-slate-200/80 bg-slate-50/60 p-5">
                <p class="text-[0.625rem] font-semibold tracking-[0.14em] text-slate-500 uppercase">Antes de publicar</p>

                <ul class="mt-3 space-y-3 text-[0.75rem] leading-5 text-slate-600">
                    <li class="flex gap-2">
                        <x-ui.icon name="check-circle" class="mt-px h-3.5 w-3.5 shrink-0 text-blue-700" />
                        Una portada horizontal y nítida: es la primera impresión del proyecto.
                    </li>
                    <li class="flex gap-2">
                        <x-ui.icon name="check-circle" class="mt-px h-3.5 w-3.5 shrink-0 text-blue-700" />
                        Una descripción con el problema, la solución y el estado actual, sin enlaces externos.
                    </li>
                    <li class="flex gap-2">
                        <x-ui.icon name="check-circle" class="mt-px h-3.5 w-3.5 shrink-0 text-blue-700" />
                        La categoría correcta: ayuda a que otras personas encuentren el proyecto.
                    </li>
                </ul>
            </div>

            <div class="rounded-xl border border-slate-200/80 bg-white p-5">
                <p class="text-[0.625rem] font-semibold tracking-[0.14em] text-slate-500 uppercase">Contacto</p>

                @if ($profile?->whatsapp_display)
                    <p class="mt-3 text-[0.75rem] leading-5 text-slate-600">
                        El botón de WhatsApp del proyecto usará el número de tu ficha institucional:
                    </p>

                    <p class="mt-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-[0.8125rem] font-medium tabular-nums text-slate-800">
                        {{ $profile->whatsapp_display }}
                    </p>
                @else
                    <p class="mt-3 text-[0.75rem] leading-5 text-slate-600">
                        Tu ficha no tiene un número de WhatsApp registrado. Los visitantes podrán ver el proyecto, pero no contactarte directamente.
                    </p>

                    <p class="mt-2 text-[0.6875rem] leading-5 text-slate-500">
                        <a href="{{ route('profile.edit') }}" class="rounded-sm font-medium text-blue-700 underline-offset-4 transition-colors duration-150 hover:text-blue-800 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2">
                            Agrega tu número en tu perfil
                        </a>
                        para que los visitantes puedan contactarte.
                    </p>
                @endif
            </div>

            @if ($editing && $project->gallery->isNotEmpty())
                <div class="rounded-xl border border-slate-200/80 bg-white p-5">
                    <p class="text-[0.625rem] font-semibold tracking-[0.14em] text-slate-500 uppercase">Galería</p>

                    <p class="mt-2 text-[0.75rem] leading-5 text-slate-500">
                        {{ $project->gallery->count() }} {{ $project->gallery->count() === 1 ? 'foto adicional' : 'fotos adicionales' }} en la página pública.
                    </p>
                </div>
            @endif

            <p class="px-1 text-[0.625rem] leading-5 text-slate-400">
                La portada y las imágenes se guardan en el almacenamiento local del portal. Al reemplazar una portada, el archivo anterior se elimina.
            </p>
        </aside>
    </div>
</form>
