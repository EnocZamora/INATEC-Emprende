@props(['current' => null, 'required' => false])

<div class="space-y-3">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <label for="diploma" class="text-[0.8125rem] font-medium text-slate-700">Diploma</label>
                <x-ui.badge :variant="$required ? 'amber' : 'neutral'">
                    {{ $required ? 'Obligatorio' : 'Opcional' }}
                </x-ui.badge>
            </div>
            <p class="mt-0.5 text-[0.6875rem] leading-5 text-slate-500">
                {{ $required ? 'Adjunta un PDF o una imagen para completar tu perfil.' : 'Puedes reemplazar o quitar el diploma guardado.' }}
            </p>
        </div>

        <span class="text-[0.6875rem] tabular-nums text-slate-400">PDF, JPG o PNG · 2 MB</span>
    </div>

    <div class="group rounded-lg border border-dashed border-slate-300 bg-slate-50/60 p-4 transition-[background-color,border-color,box-shadow,transform] duration-150 ease-out hover:border-slate-400 hover:bg-slate-50 focus-within:border-blue-400 focus-within:ring-2 focus-within:ring-blue-600/20 motion-reduce:transition-none motion-safe:active:scale-[0.99]">
        <input
            id="diploma"
            name="diploma"
            type="file"
            accept="application/pdf,image/jpeg,image/png"
            class="block w-full cursor-pointer text-[0.75rem] text-slate-600 outline-none file:me-3 file:cursor-pointer file:rounded-md file:border-0 file:bg-white file:px-3 file:py-2 file:text-[0.75rem] file:font-medium file:text-slate-700 file:shadow-[0_1px_2px_rgb(15_23_42/0.08)] file:transition-colors file:duration-150 hover:file:bg-blue-50 focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2 motion-reduce:file:transition-none"
            aria-describedby="diploma-help"
            @required($required)
        >
        <p id="diploma-help" class="mt-2 text-[0.6875rem] leading-5 text-slate-500">
            {{ $required ? 'Puedes elegir un PDF, JPG o PNG de hasta 2 MB.' : 'Un archivo nuevo reemplazará el actual al guardar.' }}
        </p>
    </div>

    @if ($current)
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-md border border-slate-200 bg-white px-3 py-2.5">
            <a href="{{ $current }}" target="_blank" rel="noopener" class="min-w-0 truncate text-[0.75rem] font-medium text-blue-700 underline-offset-4 transition-colors duration-150 hover:text-blue-800 hover:underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2 motion-reduce:transition-none">
                Ver diploma actual
            </a>
            <label class="inline-flex shrink-0 cursor-pointer items-center gap-2 rounded-sm text-[0.6875rem] text-slate-600 outline-none transition-colors duration-150 hover:text-slate-900 focus-within:ring-2 focus-within:ring-blue-600/40 focus-within:ring-offset-2 motion-reduce:transition-none">
                <input type="checkbox" name="remove_diploma" value="1" class="h-4 w-4 rounded border-slate-300 accent-blue-700 focus-visible:ring-2 focus-visible:ring-blue-600/40 focus-visible:ring-offset-2">
                Quitar diploma
            </label>
        </div>
    @endif

    @error('diploma')
        <p class="text-[0.75rem] leading-5 text-red-600">{{ $message }}</p>
    @enderror
</div>