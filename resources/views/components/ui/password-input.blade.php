@props([
    'name',
    'label',
    'hint' => 'Al menos 8 caracteres, con letras y números.',
    'strength' => false,
    'match' => null,
    'icon' => 'lock-closed',
])

@php
    $id = $attributes->get('id', $name);
    $hasError = $errors->has($name);
    $hintId = $id.'-hint';
    $errorId = $hasError ? $id.'-error' : null;
    $describedBy = collect([$hintId, $errorId])->filter()->implode(' ');

    $tone = $hasError
        ? 'border-red-300 hover:border-red-300 focus:border-red-500 focus:ring-red-500/10'
        : 'border-slate-300/90 hover:border-slate-400 focus:border-blue-600 focus:ring-blue-600/10';
@endphp

<div x-data="passwordField({ strength: @js($strength), matchField: @js($match) })">
    <div class="flex items-baseline justify-between gap-3">
        <label for="{{ $id }}" class="text-[0.8125rem] font-medium text-slate-700">{{ $label }}</label>
        {{ $action ?? '' }}
    </div>

    <div class="group relative mt-1.5">
        <span class="pointer-events-none absolute inset-y-0 start-0 flex w-10 items-center justify-center text-slate-400 transition-colors duration-150 group-focus-within:text-blue-600">
            <x-ui.icon :name="$icon" class="h-[1.125rem] w-[1.125rem]" />
        </span>

        <input
            id="{{ $id }}"
            name="{{ $name }}"
            x-model="value"
            :type="visible ? 'text' : 'password'"
            @keydown="caps = $event.getModifierState?.('CapsLock') ?? false"
            @keyup="caps = $event.getModifierState?.('CapsLock') ?? false"
            @blur="caps = false"
            @if ($hasError) aria-invalid="true" @endif
            aria-describedby="{{ $describedBy }}"
            {{ $attributes->merge([
                'class' => 'block h-11 w-full rounded-lg border bg-white ps-10 pe-11 text-base text-slate-900 shadow-[0_1px_1px_rgb(15_23_42/0.03)] outline-none transition-[border-color,box-shadow] duration-150 ease-out placeholder:text-slate-400 focus:ring-4 sm:text-[0.9375rem] '.$tone,
            ]) }}
        >

        <button
            type="button"
            @click="visible = !visible"
            :aria-label="visible ? 'Ocultar contraseña' : 'Mostrar contraseña'"
            :aria-pressed="visible"
            class="absolute inset-y-0 end-0 flex w-11 items-center justify-center rounded-e-lg text-slate-400 transition-colors duration-150 hover:text-slate-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-blue-600/40 motion-safe:active:scale-95"
        >
            <x-ui.icon name="eye" class="h-[1.125rem] w-[1.125rem]" x-show="!visible" />
            <x-ui.icon name="eye-slash" class="h-[1.125rem] w-[1.125rem]" x-show="visible" x-cloak />
        </button>
    </div>

    {{-- One reserved row: hint, Caps Lock warning, or confirmation. No layout shift. --}}
    <div id="{{ $hintId }}" class="mt-1.5 min-h-[1.25rem] text-[0.75rem] leading-5">
        <p x-show="caps" x-cloak role="status" class="flex items-center gap-1 font-medium text-amber-700">
            <x-ui.icon name="exclamation-triangle" class="h-3.5 w-3.5" />
            Bloq Mayús está activado.
        </p>
        <p x-show="!caps && !matched" class="text-slate-500">{{ $hint }}</p>
        <p x-show="!caps && matched" x-cloak class="flex items-center gap-1 font-medium text-emerald-700">
            <x-ui.icon name="check" class="h-3.5 w-3.5" />
            Las contraseñas coinciden.
        </p>
    </div>

    @if ($hasError)
        <p id="{{ $errorId }}" class="mt-1 text-[0.75rem] leading-5 text-red-600">{{ $errors->first($name) }}</p>
    @endif

    @if ($strength)
        <div class="mt-2.5" x-show="value.length > 0" x-cloak>
            <div
                class="flex gap-1.5"
                role="progressbar"
                aria-valuemin="0"
                aria-valuemax="4"
                :aria-valuenow="score"
                aria-label="Seguridad de la contraseña"
            >
                <template x-for="index in 4" :key="index">
                    <span class="h-1 flex-1 rounded-full transition-colors duration-200" :class="index <= score ? scoreTone : 'bg-slate-200'"></span>
                </template>
            </div>
            <p class="mt-1.5 text-[0.6875rem] font-medium tracking-[0.01em]" :class="scoreText">
                Seguridad: <span x-text="scoreLabel"></span>
            </p>
        </div>
    @endif
</div>
