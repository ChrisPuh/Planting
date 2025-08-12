<?php

use App\Domains\Admin\Plants\Services\PlantService;
use App\Domains\Admin\Plants\ViewModels\Show\PlantViewModel;
use App\Domains\Admin\Plants\ValueObjects\TimelineEvent;
use Livewire\Volt\Component;

new class extends Component {
    protected PlantViewModel $plant;

    public function mount(int $id, PlantService $plantService): void
    {
        $this->plant = $plantService->getPlantForShow($id);
    }
}; ?>

<x-plants.layout.show
    :id="$this->plant->id"
    :name="$this->plant->name"
    :isDeleted="$this->plant->isDeleted()"
    :wasUserCreateRequested="$this->plant->wasUserCreateRequest()"
>
    <x-plants.cards.plant-show-card
        :header="$this->plant->getHeader()"
        :details="$this->plant->getDetails()"
        :metadata="$this->plant->getMetadata()"
        :actions="$this->plant->getActions()"
    />
</x-plants.layout.show>
