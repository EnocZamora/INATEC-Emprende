@props(['name', 'id' => null])

@php
    $id = $id ?? $name;
    $hasError = $errors->has($name);
@endphp

<label for="{{ $id }}" class="group flex cursor-pointer items-start gap-2.5">
    <span class="relative mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center">
        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="checkbox"
            value="1"
            @checked(old($name))
            @if ($hasError) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
            {{ $attributes->except('class')->merge([
                'class' => 'peer h-4 w-4 cursor-pointer appearance-none rounded-[0.25rem] border bg-white transition-colors duration-150 checked:border-blue-700 checked:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 '.(($hasError ? 'border-red-400' : 'border-slate-300 hover:border-slate-400').' focus-visible:ring-blue-600/40'),
            ]) }}
        >
        <svg class="pointer-events-none absolute h-3 w-3 text-white opacity-0 transition-opacity duration-100 peer-checked:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
        </svg>
    </span>

    <span class="text-[0.8125rem] leading-5 text-slate-600">{{ $slot }}</span>
</label>

@if ($hasError)
    <p id="{{ $id }}-error" class="mt-1.5 ps-[1.625rem] text-[0.75rem] leading-5 text-red-600">{{ $errors->first($name) }}</p>
@endif
