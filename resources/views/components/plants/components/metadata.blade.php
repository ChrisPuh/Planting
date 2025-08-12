@props(['metadata'])

<div class="text-xs space-y-1">
    @foreach($metadata->getVisibleItems() as $item)
        <div class="{{ $item->colorClass }}">
            {{ $item->label }}
            @if($item->hasBy())
                von <span class="font-medium">{{ $item->by }}</span>
            @endif
            @if($item->hasAt())
                am {{ $item->at }}
            @endif
        </div>
    @endforeach
</div>
