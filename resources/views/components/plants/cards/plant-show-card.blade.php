@props([
    'id',
    'name',
    'type',
    'isDeleted',
    'category' => null,
    'latinName' => null,
    'description' => null,
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
                <flux:badge color="sky">{{__('UserRequest')}}</flux:badge>
            @endif

            @if($deletedAt)
                <flux:badge variant="solid" color="red">Gelöscht</flux:badge>
            @endif

        </div>
    </div>
    <flux:separator/>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
        @if($category)
            <div>
                <span class="font-semibold">Kategorie:</span>
                <span>{{ $category }}</span>
            </div>
        @endif

        @if($latinName)
            <div>
                <span class="font-semibold">Botanischer Name:</span>
                <span class="italic">{{ $latinName }}</span>
            </div>
        @endif
    </div>

    @if($description)
        <flux:separator/>
        <p class="text-zinc-700 dark:text-zinc-300">
            {{ $description }}
        </p>
    @endif

    <flux:separator/>


    <div class="flex items-center justify-between">
        <div>
            @if($createdBy || $createdAt || $updatedBy || $updatedAt || $requestedBy || $requestedAt || $deletedAt || $deletedBy)

                <div class="text-xs text-zinc-500 dark:text-zinc-400 space-y-1">
                    @if($createdBy || $createdAt)
                        <div>
                            Erstellt
                            @if($createdBy)
                                von <span class="font-medium">{{ $createdBy }}</span>
                            @endif
                            @if($createdAt)
                                am {{ \Carbon\Carbon::parse($createdAt)->format('d.m.Y H:i') }}
                            @endif
                        </div>
                    @endif

                    @if($updatedBy || $updatedAt)
                        <div>
                            Zuletzt geändert
                            @if($updatedBy)
                                von <span class="font-medium">{{ $updatedBy }}</span>
                            @endif
                            @if($updatedAt)
                                am {{ \Carbon\Carbon::parse($updatedAt)->format('d.m.Y H:i') }}
                            @endif
                        </div>
                    @endif
                    @if($requestedBy || $requestedAt)
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">
                            Beantragt
                            @if(auth()->user()?->is_admin && $requestedBy)
                                von <span class="font-medium">{{ $requestedBy }}</span>
                            @endif
                            @if($requestedAt)
                                am {{ \Carbon\Carbon::parse($requestedAt)->format('d.m.Y H:i') }}
                            @endif
                        </div>
                    @endif

                    @if($deletedAt)
                        <div class="text-xs text-red-500 dark:text-red-400">
                            Gelöscht
                            @if(auth()->user()?->is_admin && $deletedBy)
                                von <span class="font-medium">{{ $deletedBy }}</span>
                            @endif
                            am {{ \Carbon\Carbon::parse($deletedAt)->format('d.m.Y H:i') }}
                        </div>
                    @endif
                </div>
            @endif
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
