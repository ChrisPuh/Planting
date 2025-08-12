@props([
    'contribute'
])

<flux:modal.trigger name="contribute-plant-{{$contribute['id']}}-{{$contribute['name']}}">
    <div class="flex items-center space-x-2">
        <span>n/a</span>
        <flux:badge color="sky" class="cursor-pointer">{{__('help us out')}}</flux:button>
    </div>
</flux:modal.trigger>

<flux:modal name="contribute-plant-{{$contribute['id']}}-{{$contribute['name']}}" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Update {{$contribute['label']}}</flux:heading>
            <flux:text class="mt-2">erfasse hier das fehlende Detail ( {{$contribute['name']}} )</flux:text>
        </div>

        <flux:input label=" {{$contribute['name']}} " placeholder=" {{$contribute['name']}} "/>


        <div class="flex gap-2">
            <flux:spacer/>

            <flux:modal.close>
                <flux:button variant="ghost">{{__('Abbrechen')}}</flux:button>
            </flux:modal.close>
            <!-- TODO implement the contribute to plants-->
            <flux:button type="submit" variant="primary">{{__('Ja bitte löschen')}}</flux:button>
        </div>
    </div>
</flux:modal>
