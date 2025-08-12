@props([
    'title',
    'description' => null
])

<section class="w-full">
    <div class="relative  mb-6 w-full">

        <flux:heading size="xl" level="1">{{__('Plants')}}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{__('Verwalte die interne Pflanzendatenbank')}}</flux:subheading>


        <flux:separator variant="subtle"/>
    </div>

    <x-plants.layout :title="$title" :description="$description">
        @if(isset($actions))
            <x-slot:actions>
                {{$actions}}
            </x-slot:actions>
        @endif
        {{$slot}}
    </x-plants.layout>
</section>
