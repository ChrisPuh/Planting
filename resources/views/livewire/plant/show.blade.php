<?php

// resources/views/livewire/plant/show.blade.php - Mit protected Property
use App\Domains\Admin\Plants\Services\PlantService;
use App\Domains\Admin\Plants\ViewModels\Show\PlantViewModel;
use Livewire\Volt\Component;

new class extends Component {
    protected PlantViewModel $plant;

    public function mount(string $uuid, PlantService $plantService): void
    {
        $this->plant = $plantService->getPlantForShow($uuid);
    }

    // Getter für Template Access
    public function getPlantProperty(): PlantViewModel
    {
        return $this->plant;
    }
}; ?>

<x-plants.layout.show
    :uuid="$this->plant->uuid"
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
