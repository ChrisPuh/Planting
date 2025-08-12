@props([
    'id',
    'name',
    'type',
    'isDeleted',
    'isUpdated',
    'wasUserCreateRequested',
    'details',
    'metadata',
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
        <div class="flex flex-wrap gap-2 mt-1">
            @if($metadata->wasUserCreateRequest())
                <flux:badge color="sky">{{__('Created By User')}}</flux:badge>
            @endif

            @if($metadata->isDeleted())
                <flux:badge variant="solid" color="red">Gelöscht</flux:badge>
            @endif

        </div>
    </div>
    <flux:separator/>

    @include('partials.plants.show.details')

    <flux:separator/>

    @include('partials.plants.show.metadata')

    <flux:separator/>

    <div>
        <div class="flex justify-end gap-2">
            <x-plants.buttons.back :href="route('plants.index')"/>
            @if(!$isDeleted)
                <x-plants.buttons.delete :name="$name" :id="$id"/>
            @else
                <x-plants.buttons.restore :id="$id" :name="$name"/>
            @endif
        </div>
    </div>
</x-card>
