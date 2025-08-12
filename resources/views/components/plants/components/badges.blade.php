@props(['badges'])


@if($badges->hasBadges())
    <div class="flex flex-wrap gap-2 mt-1">
        @foreach($badges->all() as $badge)
            @php
                // check if badge->variant is set and if it is thand create a variant  to use it below
                $variant = $badge->variant ?? 'default';


            @endphp
            <flux:badge
                color="{{ $badge->color }}"
                variant="{{$variant}}"
            >
                {{ $badge->text }}
            </flux:badge>
        @endforeach
    </div>
@endif
