@props([
    'heading' => $title,
    'subheading' => $description
])

<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <flux:navlist>
            <flux:navlist.item :href="route('plants.dashboard')"
                               wire:navigate>{{ __('Zusammenfassung') }}</flux:navlist.item>
            <flux:navlist.item :href="route('plants.index')"
                               wire:navigate
            >{{ __('Alle Pflanzen') }}
            </flux:navlist.item>
            <flux:navlist.item :href="route('plants.create')"
                               wire:navigate
            >{{ __('Erstelle eine Pflanze') }}
            </flux:navlist.item>

        </flux:navlist>
    </div>

    <flux:separator class="md:hidden"/>

    <div class="flex-1 self-stretch max-md:pt-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading>{{ $heading ?? ''}}</flux:heading>
                @if(isset($subheading))
                    <flux:subheading>{{ $subheading }}</flux:subheading>
                @endif
            </div>
            <div>
                {{$actions ?? ''}}
            </div>
        </div>

        <div class="mt-5 w-full">
            {{ $slot }}
        </div>
    </div>
</div>
