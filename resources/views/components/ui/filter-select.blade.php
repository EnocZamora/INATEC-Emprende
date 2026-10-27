@props([
    'name',
    'label',
    'resets' => [],
    'disabled' => false,
])

@php
    $id = $attributes->get('id', $name);

    // Changing a parent filter invalidates its children. Clear them before the
    // request so a stale municipality cannot silently empty the results.
    $resetScript = collect($resets)
        ->map(fn (string $target): string => "document.getElementById('".e($target)."')?.value = '';")
        ->implode(' ');

    $onChange = $disabled ? null : trim($resetScript.' this.form?.requestSubmit();');
@endphp

<div>
    <label for="{{ $id }}" class="text-[0.75rem] font-medium text-slate-600">{{ $label }}</label>

    <div class="relative mt-1.5">
        <select
            id="{{ $id }}"
            name="{{ $name }}"
            @disabled($disabled)
            @if ($onChange) onchange="{{ $onChange }}" @endif
            {{ $attributes->except('id')->merge([
                'class' => 'block h-10 w-full appearance-none rounded-lg border border-slate-300/90 bg-white ps-3 pe-9 text-[0.8125rem] text-slate-700 shadow-[0_1px_1px_rgb(15_23_42/0.03)] outline-none transition-[border-color,box-shadow] duration-150 ease-out hover:border-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 disabled:cursor-not-allowed disabled:border-slate-200 disabled:bg-slate-50 disabled:text-slate-400 disabled:shadow-none [&_option]:text-slate-900',
            ]) }}
        >
            {{ $slot }}
        </select>

        <span class="pointer-events-none absolute inset-y-0 end-0 flex w-9 items-center justify-center text-slate-400">
            <x-ui.icon name="chevron-down" class="h-3.5 w-3.5" />
        </span>
    </div>
</div>
