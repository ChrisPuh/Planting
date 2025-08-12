<?php

namespace App\Domains\Admin\Plants\DTOs;

class PlantViewDTO
{
    private readonly PlantDetailsDTO $details;
    private readonly PlantMetadataDTO $metadata;

    public function __construct(
        public ?int    $id = null,

        /** TODO create ValueObject for name */
        public string  $name,
        /** TODO create Enum for type */
        public string  $type,
        /** TODO create ValueObject for image */
        public ?string $image_url = null,

        // Details
        ?string        $category = null,
        ?string        $latin_name = null,
        ?string        $description = null,

        // Metadata
        ?string        $requested_by = null,
        ?string        $requested_at = null,
        ?string        $created_by = null,
        ?string        $created_at = null,
        ?string        $updated_by = null,
        ?string        $updated_at = null,
        ?string        $deleted_by = null,
        ?string        $deleted_at = null,
    )
    {
        $this->details = PlantDetailsDTO::from($category, $latin_name, $description, $this->id);
        $this->metadata = PlantMetadataDTO::from(
            $requested_by,
            $requested_at,
            $created_by,
            $created_at,
            $updated_by,
            $updated_at,
            $deleted_by,
            $deleted_at,
            auth()->user()?->is_admin ?? false
        );
    }

    public function getDetails(): PlantDetailsDTO
    {
        return $this->details;
    }

    public function getMetadata(): PlantMetadataDTO
    {
        return $this->metadata;
    }

    // Delegated methods for backward compatibility
    public function wasUserCreateRequest(): bool
    {
        return $this->metadata->wasUserCreateRequest();
    }

    public function isDeleted(): bool
    {
        return $this->metadata->isDeleted();
    }

    public function isUpdated(): bool
    {
        return $this->metadata->hasUpdated();
    }

    /**
     * @deprecated Use getMetadata()->formattedCreatedAt() instead
     */
    public function formattedCreatedAt(): ?string
    {
        return $this->metadata->getCreated()->at;
    }

    // ... weitere deprecated methods für backward compatibility
}
