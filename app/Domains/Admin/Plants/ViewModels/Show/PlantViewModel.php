<?php

// App\Domains\Admin\Plants\ViewModels\Show\PlantViewModel.php - Updated

namespace App\Domains\Admin\Plants\ViewModels\Show;

class PlantViewModel
{
    private readonly PlantHeaderViewModel $header;

    private readonly PlantDetailsViewModel $details;

    private readonly PlantMetadataViewModel $metadata;

    private readonly PlantActionsViewModel $actions;

    private readonly PlantBadgesViewModel $badges;

    public function __construct(
        public ?string $uuid,    // ← NEU für echte Daten
        public string $name,
        public string $type,
        public ?string $image_url = null,

        // Details
        ?string $category = null,
        ?string $latin_name = null,
        ?string $description = null,

        // Metadata
        ?string $requested_by = null,
        ?string $requested_at = null,
        ?string $created_by = null,
        ?string $created_at = null,
        ?string $updated_by = null,
        ?string $updated_at = null,
        ?string $deleted_by = null,
        ?string $deleted_at = null,

        // NEW: Timeline Events
        array $timelineEvents = [],
    ) {
        $this->metadata = PlantMetadataViewModel::from(
            $requested_by, $requested_at, $created_by, $created_at,
            $updated_by, $updated_at, $deleted_by, $deleted_at,
            auth()->user()?->is_admin ?? false,
            $timelineEvents
        );

        $this->badges = PlantBadgesViewModel::from($this->metadata);
        $this->header = PlantHeaderViewModel::from($this->name, $this->type, $this->image_url, $this->badges);
        $this->details = PlantDetailsViewModel::from($category, $latin_name, $description, $this->uuid);
        $this->actions = PlantActionsViewModel::from(
            $this->uuid, // Fallback für Tests
            $this->name,
            $this->metadata->isDeleted()
        );
    }

    public function getHeader(): PlantHeaderViewModel
    {
        return $this->header;
    }

    public function getDetails(): PlantDetailsViewModel
    {
        return $this->details;
    }

    public function getMetadata(): PlantMetadataViewModel
    {
        return $this->metadata;
    }

    public function getActions(): PlantActionsViewModel
    {
        return $this->actions;
    }

    public function getBadges(): PlantBadgesViewModel
    {
        return $this->badges;
    }

    // Convenience methods
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
}
