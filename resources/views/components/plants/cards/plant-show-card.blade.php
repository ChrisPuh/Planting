@props([
    'id',
    'name',
    'type',
    'isDeleted',
    'isUpdated',
    'wasUserCreateRequested',
    'details',
    'imageUrl' => null,
    'requestedAt'=> null,
    'requestedBy'=> null,
    'deletedAt'=> null,
    'deletedBy'=> null,
    'createdAt'=>null,
    'createdBy'=>null,
    'updatedBy'=>null,
    'updatedAt'=>null,
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
            @if($requestedAt)
                <flux:badge color="sky">{{__('Created By User')}}</flux:badge>
            @endif

            @if($deletedAt)
                <flux:badge variant="solid" color="red">Gelöscht</flux:badge>
            @endif

        </div>
    </div>
    <flux:separator/>

    @include('partials.plants.show.details')

    <flux:separator/>


    <div class="flex items-center justify-between">
        <div>
            <div class="text-xs text-zinc-500 dark:text-zinc-400 space-y-1">
                <div>
                    Erstellt
                    @if($createdBy)
                        von <span class="font-medium">{{ $createdBy }}</span>
                    @endif
                    @if($createdAt)
                        am {{ $createdAt }}
                    @endif
                </div>
                @if($isUpdated || $wasUserCreateRequested || $isDeleted)

                    @if($updatedBy || $updatedAt)
                        <div>
                            Zuletzt geändert
                            @if($updatedBy)
                                von <span class="font-medium">{{ $updatedBy }}</span>
                            @endif
                            @if($updatedAt)
                                am {{ $updatedAt }}
                            @endif
                        </div>
                    @endif
                    @if($wasUserCreateRequested)
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">
                            Beantragt
                            @if(auth()->user()?->is_admin && $requestedBy)
                                von <span class="font-medium">{{ $requestedBy }}</span>
                            @endif
                            @if($requestedAt)
                                am {{ $requestedAt }}
                            @endif
                        </div>
                    @endif

                    @if($deletedAt)
                        <div class="text-xs text-red-500 dark:text-red-400">
                            Gelöscht
                            @if(auth()->user()?->is_admin && $deletedBy)
                                von <span class="font-medium">{{ $deletedBy }}</span>
                            @endif
                            am {{ $deletedAt }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
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
    </div>
</x-card>
