<div class="grid grid-cols-2 gap-4">
    @foreach($details->toArray() as $key => $detail)
        @if($key === 'description')
            <!-- Description gets full width and centered -->
            <div class="col-span-2 space-y-4">
                <div class="w-full flex items-baseline sm:space-y-0 md:space-x-4 flex-col lg:flex-row">
                    <div class="font-semibold text-emerald-600 text-center">{{ $detail->label }}</div>
                    @if($detail->isMissing)
                        <div class="flex justify-center">
                            <x-plants.buttons.contribute :contribute="$detail->contribution"/>
                        </div>
                    @else
                        <p class="text-zinc-700 dark:text-zinc-300 text-justify leading-relaxed">{{ $detail->value }}</p>
                    @endif
                </div>
            </div>
        @else
            <!-- Other details in responsive grid -->
            <div class="flex items-center sm:space-y-0 md:space-x-4 flex-col lg:flex-row">
                <div class="font-semibold text-emerald-600">{{ $detail->label }}</div>
                <div>
                    @if($detail->isMissing)
                        <x-plants.buttons.contribute :contribute="$detail->contribution"/>
                    @else
                        <span
                            class="bg-zinc-50 dark:bg-zinc-700/50 px-3 py-1.5 rounded-md text-zinc-700 dark:text-zinc-300 inline-block w-full text-center">{{ $detail->value }}</span>
                    @endif
                </div>
            </div>
        @endif
    @endforeach
</div>
