{{-- resources/views/components/plants/components/section.blade.php - Mit Separators --}}
@props(['viewModel'])

@php
    $isExpandable = method_exists($viewModel, 'isExpandable') ? $viewModel->isExpandable() : false;
    $defaultExpanded = method_exists($viewModel, 'getDefaultExpanded') ? $viewModel->getDefaultExpanded() : true;
@endphp

<div @if($isExpandable) x-data="{
    expanded: @js($defaultExpanded),
    toggle() { this.expanded = !this.expanded }
}" @endif class="space-y-3">

    {{-- Header --}}
    @if($viewModel->hasTitle())
        <flux:separator :text="$viewModel->getTitle()"/>

        @if($isExpandable)
            {{-- Expandable Section Header --}}
            <div
                @click="toggle()"
                class="flex items-center justify-between cursor-pointer group hover:bg-zinc-50 dark:hover:bg-zinc-800/50 -mx-2 px-2 py-2 rounded-lg transition-all duration-200"
            >
                <div class="flex items-center gap-3 flex-1">
                    <h3 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">
                        {{ $viewModel->getTitle() }}
                    </h3>

                    {{-- Preview für Timeline --}}
                    @if($viewModel->getVariableName() === 'metadata')
                        <div x-show="!expanded"
                             class="flex items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400">
                            <span>{{ $viewModel->getTimelineEvents()->count() }} Ereignisse</span>
                            <span>•</span>
                            <span>{{ $viewModel->getTimelineEvents()->last()?->label ?? 'Keine Events' }}</span>
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    @if($viewModel->getVariableName() === 'metadata')
                        <span
                            class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">
                            {{ $viewModel->getTimelineEvents()->count() }}
                        </span>
                    @endif

                    <flux:icon
                        name="chevron-right"
                        class="w-5 h-5 text-zinc-400 group-hover:text-zinc-600 dark:group-hover:text-zinc-300 transition-all duration-200"
                        x-bind:class="{ 'rotate-90': expanded }"
                    />
                </div>
            </div>
        @else
            {{-- Non-expandable Section mit normalem Separator  --}}
        @endif
    @else
        {{-- Header/Actions ohne Title sind immer sichtbar --}}
        @php ${$viewModel->getVariableName()} = $viewModel; @endphp
        {{-- include the partial 'partials/plants/show/*' --}}
        @include($viewModel->getPartial())
    @endif

    {{-- Content --}}
    @if($viewModel->hasTitle())
        <div
            @if($isExpandable)
                x-show="expanded"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 max-h-0"
            x-transition:enter-end="opacity-100 max-h-screen"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 max-h-screen"
            x-transition:leave-end="opacity-0 max-h-0"
            class="overflow-hidden"
            @endif
        >
            <div @if($isExpandable) class="pt-2" @endif>
                @php ${$viewModel->getVariableName()} = $viewModel; @endphp
                @include($viewModel->getPartial())
            </div>
        </div>
    @endif
</div>
