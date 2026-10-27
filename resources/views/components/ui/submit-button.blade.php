@props(['label' => 'Continuar', 'loadingLabel' => 'Procesando…'])

{{--
    Submit button for forms whose root element owns `x-data="{ submitting: false }"`.
    Both labels occupy the same grid cell, so swapping them does not resize the
    button while the request is in flight.
--}}
<button
    type="submit"
    :disabled="submitting"
    :aria-busy="submitting"
    {{ $attributes->merge([
        'class' => 'inline-flex h-11 w-full items-center justify-center rounded-lg bg-blue-700 px-4 text-[0.875rem] font-medium text-white shadow-[0_1px_1px_rgb(15_23_42/0.08)] outline-none transition-[background-color,box-shadow,transform] duration-150 ease-out hover:bg-blue-800 focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 disabled:cursor-wait disabled:bg-blue-700/80 motion-safe:active:scale-[0.99]',
    ]) }}
>
    <span class="inline-grid place-items-center">
        <span class="col-start-1 row-start-1 inline-flex items-center gap-1.5" x-show="!submitting">
            {{ $label }}
            <x-ui.icon name="arrow-right" class="h-4 w-4" />
        </span>
        <span class="col-start-1 row-start-1 inline-flex items-center gap-2" x-show="submitting" x-cloak>
            <span class="h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/30 border-t-white [animation-duration:600ms] motion-reduce:animate-none" aria-hidden="true"></span>
            {{ $loadingLabel }}
        </span>
    </span>
</button>
