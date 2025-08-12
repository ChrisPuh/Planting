<?php

namespace App\Domains\Admin\Plants\DTOs;

use Illuminate\Support\Carbon;

class PlantViewDTO
{
    private readonly PlantDetailsDTO $details;

    public function __construct(
        public ?int    $id = null,

        /** TODO create ValueObject for name */
        public string  $name,
        /** TODO create Enum for type */
        public string  $type, // Gemüse, Blume, Gras, ...
        /** TODO create ValueObject for image */
        public ?string $image_url = null,

        /*
        |--------------------------------------------------------------
        | Details - Now properly encapsulated
        |--------------------------------------------------------------
         */
        ?string        $category = null, // Unterkategorie wie "Kräuter", "Stauden"
        ?string        $latin_name = null, // Botanischer Name
        ?string        $description = null,

        /*
        |--------------------------------------------------------------
        | Metadata
        |--------------------------------------------------------------
        | These fields are used for tracking the creation, update, and deletion of the plant records
        |--------------------------------------------------------------
        | TODO: Add more metadata as needed
        | TODO: create ValueObject for metadata
        |--------------------------------------------------------------
         */
        public ?string $requested_by = null,
        public ?string $requested_at = null,

        public ?string $created_by = null,
        public ?string $created_at = null,

        public ?string $updated_by = null,
        public ?string $updated_at = null,

        public ?string $deleted_by = null,
        public ?string $deleted_at = null,
    )
    {
        $this->details = PlantDetailsDTO::from($category, $latin_name, $description, $this->id);
    }

    public function getDetails(): PlantDetailsDTO
    {
        return $this->details;
    }

    /**
     * @return array<string, PlantDetailItemDTO>
     * @deprecated Use getDetails() instead
     */
    public function getDetailsArray(): array
    {
        return $this->details->toArray();
    }

    public function wasUserCreateRequest(): bool
    {
        return $this->requested_by !== null && $this->requested_at !== null;
    }

    public function isDeleted(): bool
    {
        return $this->deleted_at !== null;
    }

    public function isUpdated(): bool
    {
        return $this->updated_at !== null;
    }

    public function formattedCreatedAt(): ?string
    {
        return $this->created_at
            ? Carbon::parse($this->created_at)->format('d.m.Y H:i')
            : null;
    }

    public function formattedUpdatedAt(): ?string
    {
        return $this->updated_at
            ? Carbon::parse($this->updated_at)->format('d.m.Y H:i')
            : null;
    }

    public function formattedRequestedAt(): ?string
    {
        return $this->requested_at
            ? Carbon::parse($this->requested_at)->format('d.m.Y H:i')
            : null;
    }

    public function formattedDeletedAt(): ?string
    {
        return $this->deleted_at
            ? Carbon::parse($this->deleted_at)->format('d.m.Y H:i')
            : null;
    }
}
