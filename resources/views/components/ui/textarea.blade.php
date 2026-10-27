@props([
    'name',
    'label',
    'hint' => null,
    'rows' => 6,
    'maxlength' => null,
])

@php
    $id = $attributes->get('id', $name);
    $value = (string) $attributes->get('value', old($name));
    $attributes = $attributes->except('value');

    $hasError = $errors->has($name);
    $hintId = $hint ? $id.'-hint' : null;
    $errorId = $hasError ? $id.'-error' : null;
    $describedBy = collect([$hintId, $errorId])->filter()->implode(' ') ?: null;
    $initialCount = mb_strlen($value);

    $tone = $hasError
        ? 'border-red-300 hover:border-red-300 focus:border-red-500 focus:ring-red-500/10'
        : 'border-slate-300/90 hover:border-slate-400 focus:border-blue-600 focus:ring-blue-600/10';
@endphp

<div @if ($maxlength) x-data="{ count: {{ $initialCount }} }" @endif>
    <div class="flex items-baseline justify-between gap-3">
        <label for="{{ $id }}" class="text-[0.8125rem] font-medium text-slate-700">{{ $label }}</label>
        {{ $action ?? '' }}
    </div>

    <div class="mt-1.5">
        <textarea
            id="{{ $id }}"
            name="{{ $name }}"
            rows="{{ $rows }}"
            @if ($hasError) aria-invalid="true" @endif
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            @if ($maxlength)
                maxlength="{{ $maxlength }}"
                @input="count = $event.target.value.length"
            @endif
            {{ $attributes->merge([
                'class' => 'block w-full resize-y rounded-lg border bg-white px-3.5 py-3 text-base leading-6 text-slate-900 shadow-[0_1px_1px_rgb(15_23_42/0.03)] outline-none transition-[border-color,box-shadow] duration-150 ease-out placeholder:text-slate-400 focus:ring-4 sm:text-[0.9375rem] '.$tone,
            ]) }}
        >{{ $value }}</textarea>
    </div>

    <div class="mt-1.5 flex items-start justify-between gap-4">
        <div class="min-w-0">
            @if ($hasError)
                <p id="{{ $errorId }}" class="text-[0.75rem] leading-5 text-red-600">{{ $errors->first($name) }}</p>
            @elseif ($hint)
                <p id="{{ $hintId }}" class="text-[0.75rem] leading-5 text-slate-500">{{ $hint }}</p>
            @endif
        </div>

        @if ($maxlength)
            <p class="shrink-0 text-[0.6875rem] leading-5 tabular-nums text-slate-400" aria-hidden="true">
                <span x-text="count">{{ $initialCount }}</span> / {{ number_format($maxlength, 0, ',', '.') }}
            </p>
        @endif
    </div>
</div>
