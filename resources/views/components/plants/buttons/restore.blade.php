@props([
    'uuid',
    'name',
])

<flux:modal.trigger name="restore-plant-{{$uuid}}">
    <flux:button icon="arrow-path-rounded-square" variant="filled">{{__('restore')}}</flux:button>
</flux:modal.trigger>

<flux:modal name="restore-plant-{{$uuid}}" class="min-w-[22rem]">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{__('Pflanze wiederherstellen?')}}</flux:heading>

            <flux:text class="mt-2">
                <p>{{__('Du bist dabei ' . $name . ' wiederherzustellen')}}</p>
                <p>{{__('Bist du dir sicher?')}}</p>
            </flux:text>
        </div>

        <div class="flex gap-2">
            <flux:spacer/>

            <flux:modal.close>
                <flux:button variant="ghost">{{__('Abbrechen')}}</flux:button>
            </flux:modal.close>
            <!-- TODO implement the restore of plants-->
            <flux:button type="submit" variant="primary">{{__('Ja bitte wiederherstellen')}}</flux:button>
        </div>
    </div>
</flux:modal>
