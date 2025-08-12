<?php

use App\Domains\Admin\Plants\DTOs\PlantViewDTO;
use Livewire\Volt\Component;

new class extends Component {
    protected PlantViewDTO $plant;

    public function mount(int $id): void
    {
        $this->plant = new PlantViewDTO(
            id: 1,
            name: 'Rote Beete',
            type: 'Gemüse',
            image_url: 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/21/Beets-Bundle.jpg/330px-Beets-Bundle.jpg',
            category: 'Wurzelgemüse',
            latin_name: null,
            description: 'Die Rote Beete ist eine Wurzelgemüseart, die für ihre leuchtend rote Farbe bekannt ist. Sie wird oft in Salaten, Suppen und als Beilage verwendet. Reich an Vitaminen und Mineralien, ist sie auch für ihre gesundheitsfördernden Eigenschaften bekannt.',

            //requested_by: null,
            //requested_at: null,
            requested_by: auth()->user()?->is_admin ? 'Max Mustermann' : null,
            requested_at: now()->subDays(10),
            created_by: auth()->user()?->is_admin ? 'Admin User' : null,
            created_at: now()->subDays(5),
            updated_by: auth()->user()?->is_admin ? 'Max Mustermann' : null,
            updated_at: now()->subDays(2),
            //deleted_by: auth()->user()?->is_admin ? 'Admin User' : null,
            //deleted_at: now()->subDays(1),
            deleted_by: null,
            deleted_at: null,

        );
    }
}; ?>

<x-plants.layout.show
    :id="$this->plant->id"
    :name="$this->plant->name"
    :isDeleted="$this->plant->isDeleted()"
    :wasUserCreateRequested="$this->plant->wasUserCreateRequest()"
>
    <x-plants.cards.plant-show-card
        :id="$this->plant->id"
        :name="$this->plant->name"
        :type="$this->plant->type"
        :isDeleted="$this->plant->isDeleted()"
        :isUpdated="$this->plant->isUpdated()"
        :wasUserCreateRequested="$this->plant->wasUserCreateRequest()"
        :details="$this->plant->getDetails()"
        :metadata="$this->plant->getMetadata()"
        :image-url="$this->plant->image_url"
    />
</x-plants.layout.show>
