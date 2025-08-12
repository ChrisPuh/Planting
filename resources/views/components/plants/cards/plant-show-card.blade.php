@props([
    'header',
    'details',
    'metadata',
    'actions',

])

<x-card class="max-w-3xl p-6 space-y-6">
    <x-plants.components.section :view-model="$header" />
    <x-plants.components.section :view-model="$details" />
    <x-plants.components.section :view-model="$metadata" />
    <x-plants.components.section :view-model="$actions" />
</x-card>
