@props(['project'])

@php
    $owner = $project->user;
    $profile = $owner?->profile;
    $cover = filled($project->cover_photo)
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($project->cover_photo)
        : null;
    $context = collect([$profile?->career?->name, $profile?->center?->name])
        ->filter()
        ->implode(' · ');
@endphp

<article {{ $attributes->merge(['class' => 'group/card relative flex min-w-0 flex-col overflow-hidden rounded-xl border border-slate-200/80 bg-white transition-[border-color,box-shadow] duration-200 ease-out hover:border-slate-300 hover:shadow-[0_1px_2px_rgb(15_23_42/0.04),0_20px_44px_-30px_rgb(15_23_42/0.3)] focus-within:ring-2 focus-within:ring-blue-600/30 focus-within:ring-offset-2']) }}>
    <div class="relative aspect-[16/10] overflow-hidden bg-slate-100">
        {{-- The fallback stays underneath the image so a missing file never shows a broken icon. --}}
        <div class="absolute inset-0 flex items-center justify-center text-slate-300">
            <x-ui.icon name="photo" class="h-8 w-8" />
        </div>

        @if ($cover)
            <img
                src="{{ $cover }}"
                alt="Portada del proyecto {{ $project->title }}"
                loading="lazy"
                decoding="async"
                class="absolute inset-0 h-full w-full object-cover text-transparent transition-transform duration-500 ease-out motion-reduce:transition-none motion-safe:group-hover/card:scale-[1.02]"
                onerror="this.remove()"
            >
        @endif

        <span class="absolute start-3 top-3 inline-flex items-center rounded-full border border-slate-900/5 bg-white/95 px-2.5 py-1 text-[0.625rem] font-semibold tracking-[0.08em] text-slate-700 uppercase shadow-[0_1px_2px_rgb(2_6_23/0.08)] backdrop-blur-sm">
            {{ $project->category }}
        </span>
    </div>

    <div class="flex flex-1 flex-col p-4">
        <h3 class="text-[0.9375rem] leading-6 font-semibold tracking-[-0.01em] text-pretty text-slate-900">
            <a
                href="{{ route('projects.show', $project) }}"
                class="rounded-sm underline-offset-4 outline-none after:absolute after:inset-0 after:content-[''] focus-visible:underline focus-visible:decoration-blue-600/50"
            >
                {{ $project->title }}
            </a>
        </h3>

        <p class="mt-1.5 mb-4 line-clamp-2 text-[0.8125rem] leading-5 text-slate-500">
            {{ \Illuminate\Support\Str::limit($project->description, 120) }}
        </p>

        <div class="mt-auto flex items-center gap-2.5 border-t border-slate-100 pt-3.5">
            <x-ui.avatar :name="$owner?->name ?? 'INATEC'" size="xs" />

            <div class="min-w-0">
                <p class="truncate text-[0.75rem] leading-4 font-medium text-slate-700">
                    {{ $owner?->name ?? 'Estudiante INATEC' }}
                </p>
                <p class="truncate text-[0.6875rem] leading-4 text-slate-500">
                    {{ $context !== '' ? $context : 'Tecnológico Nacional' }}
                </p>
            </div>

            <x-ui.icon
                name="arrow-up-right"
                class="ms-auto h-4 w-4 shrink-0 text-slate-300 transition-[color,transform] duration-150 ease-out group-hover/card:text-blue-700 motion-reduce:transition-none"
            />
        </div>
    </div>
</article>
