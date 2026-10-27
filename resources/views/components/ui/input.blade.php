@props([
    'name',
    'label',
    'type' => 'text',
    'icon' => null,
    'prefix' => null,
    'hint' => null,
])

@php
    $id = $attributes->get('id', $name);
    $value = $attributes->get('value', old($name));
    $attributes = $attributes->except('value');

    $hasError = $errors->has($name);
    $hintId = $hint ? $id.'-hint' : null;
    $errorId = $hasError ? $id.'-error' : null;
    $describedBy = collect([$hintId, $errorId])->filter()->implode(' ') ?: null;

    $padding = match (true) {
        (bool) $prefix => 'ps-[4.5rem]',
        (bool) $icon => 'ps-10',
        default => 'ps-3.5',
    };

    $tone = $hasError
        ? 'border-red-300 hover:border-red-300 focus:border-red-500 focus:ring-red-500/10'
        : 'border-slate-300/90 hover:border-slate-400 focus:border-blue-600 focus:ring-blue-600/10';
@endphp

<div>
    <div class="flex items-baseline justify-between gap-3">
        <label for="{{ $id }}" class="text-[0.8125rem] font-medium text-slate-700">{{ $label }}</label>
        {{ $action ?? '' }}
    </div>

    <div class="group relative mt-1.5">
        @if ($prefix)
            <span aria-hidden="true" class="pointer-events-none absolute inset-y-0 start-0 z-10 flex items-center rounded-s-lg border-e px-3 text-[0.9375rem] font-medium {{ $hasError ? 'border-red-200 bg-red-50/60 text-red-500' : 'border-slate-200 bg-slate-50 text-slate-500' }}">
                {{ $prefix }}
            </span>
        @elseif ($icon)
            <span class="pointer-events-none absolute inset-y-0 start-0 flex w-10 items-center justify-center text-slate-400 transition-colors duration-150 group-focus-within:text-blue-600">
                <x-ui.icon :name="$icon" class="h-[1.125rem] w-[1.125rem]" />
            </span>
        @endif

        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ $value }}"
            @if ($hasError) aria-invalid="true" @endif
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            {{ $attributes->merge([
                'class' => 'block h-11 w-full rounded-lg border bg-white text-base text-slate-900 shadow-[0_1px_1px_rgb(15_23_42/0.03)] outline-none transition-[border-color,box-shadow] duration-150 ease-out placeholder:text-slate-400 focus:ring-4 sm:text-[0.9375rem] '.$padding.' '.$tone,
            ]) }}
        >
    </div>

    @if ($hasError)
        <p id="{{ $errorId }}" class="mt-1.5 text-[0.75rem] leading-5 text-red-600">{{ $errors->first($name) }}</p>
    @elseif ($hint)
        <p id="{{ $hintId }}" class="mt-1.5 text-[0.75rem] leading-5 text-slate-500">{{ $hint }}</p>
    @endif
</div>
