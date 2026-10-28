@props(['current' => null, 'name' => ''])

<div x-data="coverField({ current: @js($current) })" class="space-y-3">
    <div>
        <p class="text-[0.8125rem] font-medium text-slate-700">Foto de perfil</p>
        <p class="mt-0.5 text-[0.6875rem] leading-5 text-slate-500">
            Se muestra junto a tu nombre. Un retrato cuadrado y con buena luz funciona mejor.
        </p>
    </div>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
        <div class="group relative h-24 w-24 shrink-0">
            <div class="relative h-full w-full overflow-hidden rounded-full border border-slate-200 bg-slate-100 shadow-[0_1px_2px_rgb(15_23_42/0.06)] ring-4 ring-white">
                <template x-if="preview">
                    <img
                        :src="preview"
                        alt="Vista previa de la foto de perfil"
                        class="h-full w-full object-cover transition-transform duration-300 ease-out motion-reduce:transition-none motion-safe:group-hover:scale-[1.03]"
                        x-on:error="preview = null"
                    >
                </template>

                <div x-show="! preview" x-cloak class="flex h-full w-full items-center justify-center">
                    <x-ui.avatar :name="$name" size="3xl" class="border-0" />
                </div>

                <button
                    type="button"
                    @click="$refs.cover.click()"
                    class="absolute inset-0 flex items-end justify-center bg-gradient-to-t from-slate-950/65 via-slate-950/10 to-transparent pb-2.5 text-white opacity-0 outline-none transition-opacity duration-150 ease-out group-hover:opacity-100 group-focus-within:opacity-100 focus-visible:opacity-100 focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-white/70 motion-reduce:transition-none"
                    aria-label="Cambiar foto de perfil"
                >
                    <x-ui.icon name="camera" class="h-5 w-5" />
                </button>
            </div>
        </div>

        <div class="min-w-0 space-y-2">
            <div class="flex flex-wrap items-center gap-2">
                <button
                    type="button"
                    @click="$refs.cover.click()"
                    class="inline-flex h-9 items-center justify-center gap-1.5 rounded-lg border border-slate-300/90 bg-white px-3 text-[0.8125rem] font-medium text-slate-700 shadow-[0_1px_1px_rgb(15_23_42/0.03)] outline-none transition-[background-color,border-color,color,transform] duration-150 ease-out hover:border-slate-400 hover:text-slate-900 focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2 motion-safe:active:scale-[0.98]"
                >
                    <x-ui.icon name="photo" class="h-3.5 w-3.5 opacity-80" />
                    <span x-text="preview ? 'Cambiar foto' : 'Elegir foto'"></span>
                </button>

                @if ($current)
                    <button
                        type="button"
                        x-show="! removed"
                        @click="remove()"
                        class="text-[0.6875rem] font-medium text-slate-500 underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2"
                    >
                        Quitar foto
                    </button>
                @endif

                <button
                    type="button"
                    x-show="filename"
                    x-cloak
                    @click="clear()"
                    class="text-[0.6875rem] font-medium text-slate-500 underline-offset-4 transition-colors duration-150 hover:text-slate-900 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2"
                >
                    Descartar
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
            </div>

            <p class="text-[0.6875rem] leading-5 text-slate-500" x-show="filename" x-cloak>
                <span x-text="filename"></span>
                <span class="text-slate-400">·</span>
                <span class="tabular-nums" x-text="filesize"></span>
            </p>

            <p class="text-[0.6875rem] leading-5 text-slate-400" x-show="removed" x-cloak>
                La foto actual se eliminará al guardar.
            </p>

            <p class="text-[0.6875rem] leading-5 text-red-600" x-show="error" x-text="error"></p>

            @error('avatar')
                <p class="text-[0.75rem] leading-5 text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <input
        x-ref="cover"
        id="avatar"
        name="avatar"
        type="file"
        accept="image/jpeg,image/png,image/webp"
        class="sr-only"
        aria-label="Foto de perfil"
        @change="choose($event)"
    >

    <input type="hidden" name="remove_avatar" :value="removed ? '1' : '0'">
</div>
