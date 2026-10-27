@props(['status'])

@php
    $states = [
        'published' => ['label' => 'Publicado', 'variant' => 'green', 'dot' => true],
        'draft' => ['label' => 'Borrador', 'variant' => 'neutral', 'dot' => false, 'icon' => 'pencil-square'],
        'suspended' => ['label' => 'Suspendido', 'variant' => 'red', 'dot' => false, 'icon' => 'alert-circle'],
    ];

    $state = $states[$status] ?? $states['draft'];
@endphp

<x-ui.badge :variant="$state['variant']" :dot="$state['dot'] ?? false" :icon="$state['icon'] ?? null">
    {{ $state['label'] }}
</x-ui.badge>
