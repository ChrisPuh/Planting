<div class="flex justify-end gap-2">
    <x-plants.buttons.back :href="$actions->getBackRoute()"/>

    @php $primaryAction = $actions->getPrimaryAction(); @endphp

    @if($primaryAction['type'] === 'delete')
        <x-plants.buttons.delete
            :name="$primaryAction['props']['name']"
            :id="$primaryAction['props']['id']"
        />
    @else
        <x-plants.buttons.restore
            :id="$primaryAction['props']['id']"
            :name="$primaryAction['props']['name']"
        />
    @endif
</div>
