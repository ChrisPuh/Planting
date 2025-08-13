{{-- resources/views/partials/plants/show/actions.blade.php --}}
<div class="flex justify-end gap-2">
    <x-plants.buttons.back :href="$actions->getBackRoute()"/>

    @php $primaryAction = $actions->getPrimaryAction(); @endphp

    @if($primaryAction['type'] === 'delete')
        <x-plants.buttons.delete
            :name="$primaryAction['props']['name']"
            :uuid="$primaryAction['props']['uuid']"
        />
    @else
        <x-plants.buttons.restore
            :uuid="$primaryAction['props']['uuid']"
            :name="$primaryAction['props']['name']"
        />
    @endif
</div>
