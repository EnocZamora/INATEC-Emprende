@props([
    'name',
    'label',
    'hint' => null,
    'prefix' => '+505',
    'maxlength' => 8,
])

@php
    $id = $attributes->get('id', $name);
    $max = (int) $maxlength;
    $national = mb_substr(
        \App\Support\WhatsAppNumber::national($attributes->get('value', old($name))),
        0,
        $max,
    );
    $attributes = $attributes->except('value');

    $hasError = $errors->has($name);
    $hintId = $hint ? $id.'-hint' : null;
    $errorId = $hasError ? $id.'-error' : null;
    $describedBy = collect([$hintId, $errorId])->filter()->implode(' ') ?: null;

    // Built as a variable: Blade would swallow the braces of an inline `{8}`.
    $pattern = '[0-9]{'.$max.'}';

    $tone = $hasError
        ? 'border-red-300 hover:border-red-300 focus:border-red-500 focus:ring-red-500/10'
        : 'border-slate-300/90 hover:border-slate-400 focus:border-blue-600 focus:ring-blue-600/10';
@endphp

{{--
    Numeric-only phone field.

    The input accepts exactly the 8-digit national number; the Alpine handler
    drops every other character as it is typed or pasted, and the counter turns
    amber before the form is submitted so an incomplete number is caught before
    a round trip. The server keeps the final word (`[578]\d{7}` with the 505
    prefix) and renders its message in the same reserved row.
--}}
<div x-data="phoneField({ max: {{ $max }}, initial: @js($national) })">
    <div class="flex items-baseline justify-between gap-3">
        <label for="{{ $id }}" class="text-[0.8125rem] font-medium text-slate-700">{{ $label }}</label>

        <p
            class="shrink-0 text-[0.6875rem] leading-5 tabular-nums transition-colors duration-150"
            :class="incomplete ? 'text-amber-600' : 'text-slate-400'"
            aria-hidden="true"
        >
            <span x-text="value.length">{{ strlen($national) }}</span> / {{ $max }}
        </p>
    </div>

    <div class="group relative mt-1.5">
        <span
            aria-hidden="true"
            class="pointer-events-none absolute inset-y-0 start-0 z-10 flex items-center rounded-s-lg border-e px-3 text-[0.9375rem] font-medium {{ $hasError ? 'border-red-200 bg-red-50/60 text-red-500' : 'border-slate-200 bg-slate-50 text-slate-500' }}"
        >
            {{ $prefix }}
        </span>

        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="tel"
            value="{{ $national }}"
            inputmode="numeric"
            pattern="{{ $pattern }}"
            maxlength="{{ $max }}"
            autocomplete="tel-national"
            @if ($hasError) aria-invalid="true" @endif
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            @input="onInput($event)"
            @blur="onBlur()"
            {{ $attributes->merge([
                'class' => 'block h-11 w-full rounded-lg border bg-white ps-[4.5rem] text-base text-slate-900 shadow-[0_1px_1px_rgb(15_23_42/0.03)] outline-none transition-[border-color,box-shadow] duration-150 ease-out placeholder:text-slate-400 focus:ring-4 sm:text-[0.9375rem] '.$tone,
            ]) }}
        >
    </div>

    @if ($hasError)
        <p id="{{ $errorId }}" class="mt-1.5 text-[0.75rem] leading-5 text-red-600">{{ $errors->first($name) }}</p>
    @elseif ($hint)
        {{-- One reserved row: guidance, or the incomplete-number warning. --}}
        <p id="{{ $hintId }}" class="mt-1.5 text-[0.75rem] leading-5">
            <span x-show="! incomplete" class="text-slate-500">{{ $hint }}</span>

            <span x-show="incomplete" x-cloak class="flex items-center gap-1 font-medium text-amber-700">
                <x-ui.icon name="exclamation-triangle" class="h-3.5 w-3.5 shrink-0" />
                El número debe tener {{ $max }} dígitos.
            </span>
        </p>
    @endif
</div>
