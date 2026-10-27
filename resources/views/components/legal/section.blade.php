@props(['id', 'number', 'title'])

<section id="{{ $id }}" class="scroll-mt-24 border-t border-slate-200/80 pt-8 first:border-t-0 first:pt-0">
    <div class="flex gap-4 sm:gap-6">
        <span class="mt-0.5 w-6 shrink-0 text-[0.6875rem] font-semibold tabular-nums text-slate-400">{{ $number }}</span>

        <div class="min-w-0 flex-1">
            <h2 class="text-[1.0625rem] font-semibold tracking-[-0.015em] text-slate-900">{{ $title }}</h2>

            <div class="mt-2.5 space-y-3 text-[0.9375rem] leading-7 text-pretty text-slate-600 [&_a]:font-medium [&_a]:text-blue-700 [&_a]:underline-offset-4 [&_a:hover]:text-blue-800 [&_a:hover]:underline [&_li]:marker:text-slate-300 [&_strong]:font-semibold [&_strong]:text-slate-900 [&_ul]:list-disc [&_ul]:space-y-2 [&_ul]:ps-5">
                {{ $slot }}
            </div>
        </div>
    </div>
</section>
