@props([
    'id',
    'name',
])

<flux:modal.trigger name="delete-plant-{{$id}}">
    <flux:button icon="trash" variant="danger">Delete</flux:button>
</flux:modal.trigger>

<flux:modal name="delete-plant-{{$id}}" class="min-w-[22rem]">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">{{__('Pflanze löschen?')}}</flux:heading>

            <flux:text class="mt-2">
                <p>{{__('Du bist dabei ' . $name . ' zu löschen')}}</p>
                <p>{{__('Bist du dir sicher?')}}</p>
            </flux:text>
        </div>

        <div class="flex gap-2">
            <flux:spacer />

            <flux:modal.close>
                <flux:button variant="ghost">{{__('Abbrechen')}}</flux:button>
            </flux:modal.close>
            <!-- TODO implement the delete of plants-->
            <flux:button type="submit" variant="danger">{{__('Ja bitte löschen')}}</flux:button>
        </div>
    </div>
</flux:modal>
