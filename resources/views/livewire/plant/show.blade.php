<?php

use App\Domains\Admin\Plants\DTOs\PlantViewDTO;
use Livewire\Volt\Component;

new class extends Component {
    protected PlantViewDTO $plant;

    public function mount(): void
    {
        $this->plant = new PlantViewDTO(
            id: 1,
            name: 'Rote Beete',
            type: 'Gemüse',
            category: 'Wurzelgemüse',
            latin_name: 'Beta vulgaris',
            description: 'Rote Beete ist eine zweijährige Pflanze, die als Wurzelgemüse angebaut wird.',
            image_url: 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/21/Beets-Bundle.jpg/330px-Beets-Bundle.jpg',
            isDeleted: true,
            wasUserCreateRequested: true,

            requested_by: auth()->user()?->is_admin ? 'Max Mustermann' : null,
            requested_at: now()->subDays(10),
            created_by: auth()->user()?->is_admin ? 'Admin User' : null,
            created_at: now()->subDays(5),

            updated_by: auth()->user()?->is_admin ? 'Max Mustermann' : null,
            updated_at: now()->subDays(2),
            //deleted_by:null,
            //deleted_at:null
            deleted_by: auth()->user()?->is_admin ? 'Admin User' : null,
            deleted_at: now()->subDays(1),

        );
    }
}; ?>

<x-plants.layout.show
    :id="$this->plant->id"
    :name="$this->plant->name"
    :isDeleted="$this->plant->isDeleted"
    :wasUserCreateRequested="$this->plant->wasUserCreateRequested"
>
    <x-plants.cards.plant-show-card
        :id="$this->plant->id"
        :name="$this->plant->name"
        :type="$this->plant->type"
        :category="$this->plant->category"
        :latin-name="$this->plant->latin_name"
        :description="$this->plant->description"
        :image-url="$this->plant->image_url"
        :createdAt="$this->plant->created_at"
        :createdBy="$this->plant->created_by"
        :updatedBy="$this->plant->updated_by"
        :updatedAt="$this->plant->updated_at"
        :requestedBy="$this->plant->requested_by"
        :requestedAt="$this->plant->requested_at"
        :deletedBy="$this->plant->deleted_by"
        :deletedAt="$this->plant->deleted_at"
        :isDeleted="$this->plant->isDeleted"


    />
</x-plants.layout.show>
