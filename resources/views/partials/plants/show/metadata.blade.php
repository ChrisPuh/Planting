{{-- resources/views/partials/plants/show/metadata.blade.php - Enhanced Timeline --}}
<div class="space-y-0">
    @foreach($metadata->getTimelineEvents() as $index => $event)
        <div class="flex items-start gap-4 {{ $loop->last ? 'pb-0' : 'pb-4' }}">
            {{-- Timeline Line & Icon --}}
            <div class="flex-shrink-0 relative flex flex-col items-center">
                {{-- Icon --}}
                <div class="w-10 h-10 rounded-full bg-white dark:bg-zinc-800 border-2 border-zinc-200 dark:border-zinc-700 flex items-center justify-center shadow-sm">
                    <flux:icon :name="$event->iconName" class="w-5 h-5 {{ $event->iconColor }}" />
                </div>

                {{-- Timeline Line (except for last item) --}}
                @if(!$loop->last)
                    <div class="w-0.5 h-6 bg-zinc-200 dark:bg-zinc-700 mt-2"></div>
                @endif
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0 {{ $loop->last ? 'pb-0' : 'pb-4' }}">
                {{-- Main Content Card --}}
                <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 shadow-sm">
                    {{-- Header --}}
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-semibold {{ $event->colorClass }}">
                            {{ $event->label }}
                        </span>
                        @if($event->hasAt())
                            <time class="text-xs {{ $event->colorClass }} opacity-75 font-mono bg-zinc-50 dark:bg-zinc-700/50 px-2 py-1 rounded">
                                {{ $event->at }}
                            </time>
                        @endif
                    </div>

                    {{-- User Info --}}
                    @if($event->hasBy())
                        <div class="text-xs {{ $event->colorClass }} opacity-75 mb-2 flex items-center gap-1">
                            <flux:icon name="user" class="w-3 h-3" />
                            <span class="font-medium">{{ $event->by }}</span>
                        </div>
                    @endif

                    {{-- Details (für Updates) --}}
                    @if($event->hasDetails())
                        <div class="mt-3 pt-3 border-t border-zinc-100 dark:border-zinc-700">
                            <div class="text-xs font-medium text-zinc-600 dark:text-zinc-400 mb-2">Geänderte Felder:</div>
                            <div class="flex flex-wrap gap-1">
                                @foreach($event->details as $field)
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300">
                                        {{ $field }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Fallback wenn keine Events --}}
@if($metadata->getTimelineEvents()->isEmpty())
    <div class="text-center py-8 text-zinc-500 dark:text-zinc-400">
        <flux:icon name="clock" class="w-8 h-8 mx-auto mb-2 opacity-50" />
        <p class="text-sm">Noch keine Timeline-Ereignisse</p>
    </div>
@endif
