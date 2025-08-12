<x-plants.layout.crud :title="__('Plants')" :description="__('Hier kannst du eine neue Pflanze erstellen ')">
    <x-slot:actions>
        <x-plants.buttons.back :href="route('plants.index')"/>
    </x-slot:actions>
    {{$slot}}
</x-plants.layout.crud>
