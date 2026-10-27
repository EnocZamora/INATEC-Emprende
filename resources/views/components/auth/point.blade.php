@props(['index', 'title'])

<div class="flex gap-4 py-3.5">
    <span class="mt-px w-6 shrink-0 text-[0.6875rem] font-semibold tabular-nums text-slate-400">{{ $index }}</span>

    <div class="min-w-0">
        <p class="text-[0.8125rem] font-semibold text-slate-900">{{ $title }}</p>
        <p class="mt-0.5 text-[0.8125rem] leading-5 text-slate-500">{{ $slot }}</p>
    </div>
</div>
