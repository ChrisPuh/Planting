<x-plants.layout.crud :title="__('Plants')" :description="__('Hier siehst alle Infos zu ' . $name)">
    <x-slot:actions>
        @if(!$isDeleted && auth()->user()?->is_admin)
            <x-plants.buttons.edit :id="$id"/>

        @endif
    </x-slot:actions>
    {{$slot}}
</x-plants.layout.crud>
