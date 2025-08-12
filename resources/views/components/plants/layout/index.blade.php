<x-plants.layout.crud :title="__('Plants')" :description="__('Hier siehst du alle Pflanzen aus der Datenbank')">
    <x-slot:actions>
        <x-plants.buttons.create/>
    </x-slot:actions>
    {{$slot}}
</x-plants.layout.crud>
