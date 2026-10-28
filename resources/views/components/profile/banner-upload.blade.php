@props(['current' => null])

<div x-data="coverField({ current: @js($current) })" class="space-y-2">
    <div class="flex flex-wrap items-end justify-between gap-2">
        <div>
            <label for="banner" class="text-[0.8125rem] font-medium text-slate-700">Portada del perfil</label>
            <p class="mt-0.5 text-[0.6875rem] leading-5 text-slate-500">
                La franja que aparece detrás de tu foto en la ficha pública.
            </p>
        </div>

        <span class="text-[0.6875rem] tabular-nums text-slate-400">16:5 · JPG, PNG o WebP · 2 MB</span>
    </div>

    <div class="group relative overflow-hidden rounded-xl border border-dashed border-slate-300 bg-slate-50/60 transition-[border-color,box-shadow] duration-200 ease-out focus-within:border-blue-400 focus-within:ring-2 focus-within:ring-blue-600/20 hover:border-slate-400">
        <div class="relative aspect-[16/6] min-h-28 sm:aspect-[16/5]">
            <template x-if="preview">
                <img
                    :src="preview"
                    alt="Vista previa de la portada del perfil"
                    class="absolute inset-0 h-full w-full object-cover"
                    x-on:error="preview = null"
                >
            </template>

            <div
                x-show="! preview"
                x-cloak
                class="absolute inset-0 flex flex-col items-center justify-center gap-1.5 bg-[radial-gradient(circle_at_20%_20%,#dbeafe,transparent_55%),linear-gradient(135deg,#f8fafc,#eef2ff)] text-slate-400"
            >
                <x-ui.icon name="photo" class="h-6 w-6" />
                <p class="text-[0.75rem]">Sin portada seleccionada</p>
                <p class="text-[0.6875rem] text-slate-400">Una franja horizontal y nítida funciona mejor</p>
            </div>

            <div class="absolute inset-x-0 bottom-0 flex items-center justify-between gap-3 bg-slate-950/55 px-3 py-2 backdrop-blur-sm">
                <p class="truncate text-[0.6875rem] text-white/90" x-text="filename ?? (preview ? 'Portada actual' : 'Ningún archivo')"></p>

                <p class="shrink-0 text-[0.6875rem] tabular-nums text-white/70" x-show="filesize" x-text="filesize"></p>

                <button
                    type="button"
                    @click="$refs.cover.click()"
                    class="shrink-0 rounded-md bg-white/15 px-2 py-1 text-[0.6875rem] font-medium text-white outline-none transition-[background-color,transform] duration-150 ease-out hover:bg-white/25 focus-visible:ring-2 focus-visible:ring-white/60 motion-safe:active:scale-[0.97]"
                >
                    <span x-text="preview ? 'Cambiar' : 'Elegir imagen'"></span>
                </button>
            </div>
        </div>
    </div>

    <input
        x-ref="cover"
        id="banner"
        name="banner"
        type="file"
        accept="image/jpeg,image/png,image/webp"
        class="sr-only"
        @change="choose($event)"
    >

    <input type="hidden" name="remove_banner" :value="removed ? '1' : '0'">

    <div class="flex flex-wrap items-center gap-x-3 gap-y-1.5">
        @if ($current)
            <button
                type="button"
                x-show="! removed"
                @click="remove()"
                class="text-[0.6875rem] font-medium text-slate-500 underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2"
            >
                Quitar portada
            </button>
        @endif

        <button
            type="button"
            x-show="filename"
            x-cloak
            @click="clear()"
            class="text-[0.6875rem] font-medium text-slate-500 underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2"
        >
            Descartar selección
        </button>

        <button
            type="button"
            x-show="removed"
            x-cloak
            @click="undo()"
            class="text-[0.6875rem] font-medium text-blue-700 underline-offset-4 transition-colors duration-150 hover:text-blue-800 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2"
        >
            Deshacer
        </button>

        <p class="text-[0.6875rem] leading-5 text-slate-400" x-show="removed" x-cloak>
            La portada actual se eliminará al guardar.
        </p>
    </div>

    <p class="text-[0.6875rem] leading-5 text-red-600" x-show="error" x-text="error"></p>

    @error('banner')
        <p class="text-[0.75rem] leading-5 text-red-600">{{ $message }}</p>
    @enderror
</div>
