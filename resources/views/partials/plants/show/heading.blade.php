<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between">
    <div class="flex items-center gap-4">
        @if($header->getAvatar()->hasImage())
            <flux:avatar
                src="{{ $header->getAvatar()->src }}"
                size="{{ $header->getAvatar()->size }}"
                alt="{{ $header->getAvatar()->alt }}"
            />
        @else
            <flux:avatar
                initials="{{ $header->getAvatar()->initials }}"
                size="{{ $header->getAvatar()->size }}"
            />
        @endif
        <div>
            <h1 class="text-2xl font-bold text-emerald-600">{{ $header->name }}</h1>
            <flux:badge color="{{ $header->getTypeBadge()->color }}">
                {{ $header->getTypeBadge()->text }}
            </flux:badge>
        </div>
    </div>

    @if($header->hasBadges())
        <x-plants.components.badges :badges="$header->getBadges()"/>
    @endif
</div>
