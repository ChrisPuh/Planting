<?php

namespace App\Domains\PlantManagement\Projectors;

use App\Domains\PlantManagement\Events\PlantCreated;
use App\Domains\PlantManagement\Events\PlantDeleted;
use App\Domains\PlantManagement\Events\PlantRestored;
use App\Domains\PlantManagement\Events\PlantUpdated;
use App\Models\Plant;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class PlantProjector extends Projector
{
    /**
     * Handle PlantCreated event - creates new plant record
     */
    public function onPlantCreated(PlantCreated $event): void
    {
        Plant::query()
            ->create([
                'uuid' => $event->plantId,
                'name' => $event->name,
                'type' => $event->type,
                'category' => $event->category,
                'latin_name' => $event->latinName,
                'description' => $event->description,
                'image_url' => $event->imageUrl,
                'is_deleted' => false,
                'was_community_requested' => $event->wasUserRequested,
                'created_by' => $event->createdBy,
                'last_updated_by' => $event->createdBy,
                'last_event_at' => $event->createdAt,
            ]);
    }

    /**
     * Handle PlantUpdated event - updates existing plant record
     */
    public function onPlantUpdated(PlantUpdated $event): void
    {
        $plant = Plant::query()
            ->whereUuid($event->plantId)
            ->first();

        if (!$plant) {
            // Log error but don't throw exception to avoid breaking other projectors
            \Log::error("Plant not found for update: {$event->plantId}");
            return;
        }

        // Apply changes to the plant record
        $updateData = $event->changes;
        $updateData['last_updated_by'] = $event->updatedBy;
        $updateData['last_event_at'] = $event->updatedAt;

        $plant->update($updateData);
    }

    /**
     * Handle PlantDeleted event - soft deletes plant record
     */
    public function onPlantDeleted(PlantDeleted $event): void
    {
        $plant = Plant::whereUuid($event->plantId)->first();

        if (!$plant) {
            \Log::error("Plant not found for deletion: {$event->plantId}");
            return;
        }

        $plant->update([
            'is_deleted' => true,
            'last_updated_by' => $event->deletedBy,
            'last_event_at' => $event->deletedAt,
        ]);
    }

    /**
     * Handle PlantRestored event - restores soft deleted plant
     */
    public function onPlantRestored(PlantRestored $event): void
    {
        $plant = Plant::whereUuid($event->plantId)->first();

        if (!$plant) {
            \Log::error("Plant not found for restoration: {$event->plantId}");
            return;
        }

        $plant->update([
            'is_deleted' => false,
            'last_updated_by' => $event->restoredBy,
            'last_event_at' => $event->restoredAt,
        ]);
    }

    /**
     * Reset projection - delete all plants
     * Used for rebuilding projections from scratch
     */
    public function resetState(): void
    {
        Plant::truncate();
    }
}
