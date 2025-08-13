@props([
    'uuid'
])

<flux:button
    variant="primary"
    icon="pencil"
    href="'{{ route('plants.edit', ['uuid'=>$uuid]) }}"
>
    Bearbeiten
</flux:button>
