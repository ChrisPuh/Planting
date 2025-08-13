<?php

namespace App\Domains\PlantManagement\Aggregates;

use App\Domains\PlantManagement\Events\PlantCreated;
use App\Domains\PlantManagement\Events\PlantUpdated;
use App\Domains\PlantManagement\Events\PlantDeleted;
use App\Domains\PlantManagement\Events\PlantRestored;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class PlantAggregate extends AggregateRoot
{
    // Aggregate state properties
    private string $name = '';
    private string $type = '';
    private ?string $category = null;
    private ?string $latinName = null;
    private ?string $description = null;
    private ?string $imageUrl = null;
    private bool $isDeleted = false;
    private bool $wasUserRequested = false;
    private string $createdBy = '';
    private ?string $lastUpdatedBy = null;

    /**
     * Create a new plant with comprehensive validation
     */
    public function createPlant(
        string $name,
        string $type,
        ?string $category = null,
        ?string $latinName = null,
        ?string $description = null,
        ?string $imageUrl = null,
        bool $wasUserRequested = false,
        string $createdBy = 'System'
    ): self {
        // Business rule validation
        $this->validatePlantName($name);
        $this->validatePlantType($type);

        if ($category) {
            $this->validateCategory($category);
        }

        if ($latinName) {
            $this->validateLatinName($latinName);
        }

        if ($imageUrl) {
            $this->validateImageUrl($imageUrl);
        }

        $this->recordThat(new PlantCreated(
            plantId: $this->uuid(),
            name: trim($name),
            type: $type,
            category: $category,
            latinName: $latinName,
            description: $description,
            imageUrl: $imageUrl,
            createdBy: $createdBy,
            createdAt: now()->toISOString(),
            wasUserRequested: $wasUserRequested,
        ));

        return $this;
    }

    /**
     * Update plant with validation and change tracking
     */
    public function updatePlant(array $changes, string $updatedBy = 'System'): self
    {
        // Domain validation - cannot update deleted plants
        if ($this->isDeleted) {
            throw new \DomainException('Cannot update deleted plant');
        }

        // Validate changes contain only allowed fields
        $allowedFields = ['name', 'type', 'category', 'latin_name', 'description', 'image_url'];
        $invalidFields = array_diff(array_keys($changes), $allowedFields);

        if (!empty($invalidFields)) {
            throw new \InvalidArgumentException(
                'Invalid fields in update: ' . implode(', ', $invalidFields)
            );
        }

        // Validate individual field changes
        if (isset($changes['name'])) {
            $this->validatePlantName($changes['name']);
        }

        if (isset($changes['type'])) {
            $this->validatePlantType($changes['type']);
        }

        if (isset($changes['category'])) {
            $this->validateCategory($changes['category']);
        }

        if (isset($changes['latin_name'])) {
            $this->validateLatinName($changes['latin_name']);
        }

        if (isset($changes['image_url'])) {
            $this->validateImageUrl($changes['image_url']);
        }

        // Only record event if there are actual changes
        $filteredChanges = $this->filterActualChanges($changes);

        if (empty($filteredChanges)) {
            throw new \DomainException('No actual changes detected');
        }

        $this->recordThat(new PlantUpdated(
            plantId: $this->uuid(),
            changes: $filteredChanges,
            updatedBy: $updatedBy, // FIXED: Now uses the parameter
            //TODO now()->toISOString(), sollte als string übergeben werden
            updatedAt: now()->toISOString(),
        ));

        return $this;
    }

    /**
     * Soft delete a plant
     */
    public function deletePlant(?string $reason = null, string $deletedBy = 'System'): self
    {
        if ($this->isDeleted) {
            throw new \DomainException('Plant is already deleted');
        }

        $this->recordThat(new PlantDeleted(
            plantId: $this->uuid(),
            deletedBy: $deletedBy, // FIXED: Now uses the parameter
            //TODO now()->toISOString(), sollte als string übergeben werden
            deletedAt: now()->toISOString(),
            reason: $reason,
        ));

        return $this;
    }

    /**
     * Restore a deleted plant
     */
    public function restorePlant(string $restoredBy = 'System'): self
    {
        if (!$this->isDeleted) {
            throw new \DomainException('Plant is not deleted and cannot be restored');
        }

        $this->recordThat(new PlantRestored(
            plantId: $this->uuid(),
            restoredBy: $restoredBy, // FIXED: Now uses the parameter
            restoredAt: now()->toISOString(),
        ));

        return $this;
    }

    // ===== Event Handlers for State Reconstruction =====

    /**
     * Apply PlantCreated event to rebuild aggregate state
     */
    public function applyPlantCreated(PlantCreated $event): void
    {
        $this->name = $event->name;
        $this->type = $event->type;
        $this->category = $event->category;
        $this->latinName = $event->latinName;
        $this->description = $event->description;
        $this->imageUrl = $event->imageUrl;
        $this->wasUserRequested = $event->wasUserRequested;
        $this->createdBy = $event->createdBy;
        $this->isDeleted = false;
    }

    /**
     * Apply PlantUpdated event to rebuild aggregate state
     */
    public function applyPlantUpdated(PlantUpdated $event): void
    {
        // Apply changes to current state
        foreach ($event->changes as $field => $value) {
            match ($field) {
                'name' => $this->name = $value,
                'type' => $this->type = $value,
                'category' => $this->category = $value,
                'latin_name' => $this->latinName = $value,
                'description' => $this->description = $value,
                'image_url' => $this->imageUrl = $value,
                default => throw new \InvalidArgumentException("Unknown field: {$field}")
            };
        }

        $this->lastUpdatedBy = $event->updatedBy;
    }

    /**
     * Apply PlantDeleted event to rebuild aggregate state
     */
    public function applyPlantDeleted(PlantDeleted $event): void
    {
        $this->isDeleted = true;
    }

    /**
     * Apply PlantRestored event to rebuild aggregate state
     */
    public function applyPlantRestored(PlantRestored $event): void
    {
        $this->isDeleted = false;
    }

    // ===== Domain Validation Methods =====

    private function validatePlantName(string $name): void
    {
        $trimmedName = trim($name);

        if (empty($trimmedName)) {
            throw new \InvalidArgumentException('Plant name cannot be empty');
        }

        if (strlen($trimmedName) < 2) {
            throw new \InvalidArgumentException('Plant name must be at least 2 characters long');
        }

        if (strlen($trimmedName) > 100) {
            throw new \InvalidArgumentException('Plant name cannot exceed 100 characters');
        }

        // Check for invalid characters (only letters, numbers, spaces, hyphens, apostrophes)
        if (!preg_match('/^[a-zA-ZäöüÄÖÜß0-9\s\-\'\.]+$/u', $trimmedName)) {
            throw new \InvalidArgumentException('Plant name contains invalid characters');
        }
    }

    private function validatePlantType(string $type): void
    {
        $validTypes = ['gemuese', 'obst', 'kraeuter', 'blumen', 'baeume', 'straeucher'];

        if (!in_array($type, $validTypes)) {
            throw new \InvalidArgumentException(
                'Invalid plant type. Allowed: ' . implode(', ', $validTypes)
            );
        }
    }

    private function validateCategory(?string $category): void
    {
        if ($category === null) {
            return;
        }

        $trimmedCategory = trim($category);

        if (strlen($trimmedCategory) > 50) {
            throw new \InvalidArgumentException('Category cannot exceed 50 characters');
        }
    }

    private function validateLatinName(?string $latinName): void
    {
        if ($latinName === null) {
            return;
        }

        $trimmedLatinName = trim($latinName);

        if (strlen($trimmedLatinName) > 100) {
            throw new \InvalidArgumentException('Latin name cannot exceed 100 characters');
        }

        // Basic validation for latin names (Genus species format)
        if (!preg_match('/^[A-Z][a-z]+ [a-z]+/', $trimmedLatinName)) {
            throw new \InvalidArgumentException(
                'Latin name should follow "Genus species" format (e.g., "Solanum lycopersicum")'
            );
        }
    }

    private function validateImageUrl(?string $imageUrl): void
    {
        if ($imageUrl === null) {
            return;
        }

        if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid image URL format');
        }

        // Check if URL points to an image (basic check)
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower(pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions)) {
            throw new \InvalidArgumentException(
                'Image URL must point to a valid image file (.jpg, .jpeg, .png, .gif, .webp)'
            );
        }
    }

    /**
     * Filter out changes that don't actually change current values
     */
    private function filterActualChanges(array $changes): array
    {
        $actualChanges = [];

        foreach ($changes as $field => $newValue) {
            $currentValue = match ($field) {
                'name' => $this->name,
                'type' => $this->type,
                'category' => $this->category,
                'latin_name' => $this->latinName,
                'description' => $this->description,
                'image_url' => $this->imageUrl,
                default => null
            };

            // Only include if value actually changed
            if ($currentValue !== $newValue) {
                $actualChanges[$field] = $newValue;
            }
        }

        return $actualChanges;
    }

    // ===== Getters for Testing/Debugging =====

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function wasUserRequested(): bool
    {
        return $this->wasUserRequested;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function getLatinName(): ?string
    {
        return $this->latinName;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function getCreatedBy(): string
    {
        return $this->createdBy;
    }

    public function getLastUpdatedBy(): ?string
    {
        return $this->lastUpdatedBy;
    }
}
