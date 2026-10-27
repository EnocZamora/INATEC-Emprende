@props(['icon' => 'grid', 'title', 'description' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50/50 px-6 py-14 text-center']) }}>
    <span class="flex h-11 w-11 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-400">
        <x-ui.icon :name="$icon" class="h-5 w-5" />
    </span>

    <p class="mt-4 text-[0.9375rem] font-semibold tracking-[-0.01em] text-slate-900">{{ $title }}</p>

    @if ($description)
        <p class="mt-1.5 max-w-sm text-[0.8125rem] leading-6 text-slate-500">{{ $description }}</p>
    @endif

    @if (! $slot->isEmpty())
        <div class="mt-5">{{ $slot }}</div>
    @endif
</div>
