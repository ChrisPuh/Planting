@props([
    'id',
    'name',
    'type',
    'details',
    'metadata',
    'badges',
    'imageUrl' => null,

])

<x-card class="max-w-3xl p-6 space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
        <div class="flex items-center gap-4">
            @if($imageUrl)
                <flux:avatar src="{{ $imageUrl }}" size="xl" alt="{{ $name }}"/>
            @else
                <flux:avatar initials="{{ substr($name, 0, 1) }}" size="lg"/>
            @endif
            <div>
                <h1 class="text-2xl font-bold text-emerald-600">{{ $name }}</h1>
                <flux:badge color="lime">{{ $type }}</flux:badge>
            </div>
        </div>
        <x-plants.components.badges :badges="$badges" />

    </div>
    <flux:separator/>

    @include('partials.plants.show.details')

    <flux:separator/>

    @include('partials.plants.show.metadata')

    <flux:separator/>

    <div>
        <div class="flex justify-end gap-2">
            <x-plants.buttons.back :href="route('plants.index')"/>
            @if(!$metadata->isDeleted())
                <x-plants.buttons.delete :name="$name" :id="$id"/>
            @else
                <x-plants.buttons.restore :id="$id" :name="$name"/>
            @endif
        </div>
    </div>
</x-card>
