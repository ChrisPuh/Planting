{{-- resources/views/partials/plants/show/metadata.blade.php --}}
<div class="space-y-3">
    @foreach($metadata->getVisibleItems() as $item)
        <div class="flex items-start gap-4 p-3 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-800/50">
            {{-- Icon basierend auf Action --}}
            <div class="flex-shrink-0 mt-0.5">
                @php
                    $iconConfig = match($item->label) {
                        'Erstellt' => ['icon' => 'plus-circle', 'color' => 'text-emerald-500'],
                        'Zuletzt geändert' => ['icon' => 'pencil', 'color' => 'text-blue-500'],
                        'Beantragt' => ['icon' => 'clock', 'color' => 'text-amber-500'],
                        'Gelöscht' => ['icon' => 'trash', 'color' => 'text-red-500'],
                        default => ['icon' => 'info', 'color' => 'text-zinc-500']
                    };
                @endphp
                <div class="w-8 h-8 rounded-full bg-white dark:bg-zinc-700 border border-zinc-200 dark:border-zinc-600 flex items-center justify-center">
                    <flux:icon :name="$iconConfig['icon']" class="w-4 h-4 {{ $iconConfig['color'] }}" />
                </div>
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium {{ $item->colorClass }}">
                        {{ $item->label }}
                    </span>
                    @if($item->hasAt())
                        <span class="text-xs {{ $item->colorClass }} opacity-75 font-mono">
                            {{ $item->at }}
                        </span>
                    @endif
                </div>

                @if($item->hasBy())
                    <div class="text-xs {{ $item->colorClass }} opacity-75 mt-1">
                        von <span class="font-medium">{{ $item->by }}</span>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>
