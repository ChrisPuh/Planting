<?php

namespace App\Domains\PlantManagement\Projectors;

use App\Domains\PlantManagement\Events\PlantCreated;
use App\Domains\PlantManagement\Events\PlantDeleted;
use App\Domains\PlantManagement\Events\PlantRestored;
use App\Domains\PlantManagement\Events\PlantUpdated;
use App\Domains\RequestManagement\Events\PlantCreationRequested;
use App\Domains\RequestManagement\Events\PlantUpdateRequested;
use App\Models\PlantTimelineProjection;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class PlantTimelineProjector extends Projector
{
    /**
     * Handle PlantCreationRequested event - creates timeline entry for new plant request
     */
    public function onPlantCreationRequested(PlantCreationRequested $event): void
    {
        PlantTimelineProjection::create([
            'plant_uuid' => $event->plantId,
            'event_type' => 'requested',
            'performed_by' => $event->requestedBy,
            'performed_at' => $event->requestedAt,
            'display_text' => 'Neue Pflanze wurde beantragt',
            'event_details' => [
                'request_id' => $event->requestId,
                'proposed_data' => $event->proposedData,
                'reason' => $event->reason,
                'request_type' => 'new_plant',
            ],
            'sequence_number' => $this->getNextSequenceNumber($event->plantId),
        ]);
    }

    /**
     * Handle PlantCreated event - creates timeline entry for plant creation
     */
    public function onPlantCreated(PlantCreated $event): void
    {
        $displayText = $event->wasUserRequested
            ? "Pflanze '{$event->name}' wurde nach Community-Anfrage erstellt"
            : "Pflanze '{$event->name}' wurde erstellt";

        PlantTimelineProjection::create([
            'plant_uuid' => $event->plantId,
            'event_type' => 'created',
            'performed_by' => $event->createdBy,
            'performed_at' => $event->createdAt,
            'display_text' => $displayText,
            'event_details' => [
                'initial_data' => [
                    'name' => $event->name,
                    'type' => $event->type,
                    'category' => $event->category,
                    'latin_name' => $event->latinName,
                    'description' => $event->description,
                    'image_url' => $event->imageUrl,
                ],
                'was_user_requested' => $event->wasUserRequested,
            ],
            'sequence_number' => $this->getNextSequenceNumber($event->plantId),
        ]);
    }

    /**
     * Handle PlantUpdateRequested event - creates timeline entry for update request
     */
    public function onPlantUpdateRequested(PlantUpdateRequested $event): void
    {
        PlantTimelineProjection::create([
            'plant_uuid' => $event->plantId,
            'event_type' => 'update_requested',
            'performed_by' => $event->requestedBy,
            'performed_at' => $event->requestedAt,
            'display_text' => 'Änderung wurde beantragt',
            'event_details' => [
                'request_id' => $event->requestId,
                'proposed_changes' => $event->proposedChanges,
                'reason' => $event->reason,
                'requested_fields' => array_keys($event->proposedChanges),
            ],
            'sequence_number' => $this->getNextSequenceNumber($event->plantId),
        ]);
    }

    /**
     * Handle PlantUpdated event - creates timeline entry for plant update
     */
    public function onPlantUpdated(PlantUpdated $event): void
    {
        $changedFields = array_keys($event->changes);
        $fieldsList = $this->formatFieldsList($changedFields);

        PlantTimelineProjection::create([
            'plant_uuid' => $event->plantId,
            'event_type' => 'updated',
            'performed_by' => $event->updatedBy,
            'performed_at' => $event->updatedAt,
            'display_text' => "Pflanze wurde aktualisiert ({$fieldsList})",
            'event_details' => [
                'changes' => $event->changes,
                'changed_fields' => $changedFields,
            ],
            'sequence_number' => $this->getNextSequenceNumber($event->plantId),
        ]);
    }

    /**
     * Handle PlantDeleted event - creates timeline entry for plant deletion
     */
    public function onPlantDeleted(PlantDeleted $event): void
    {
        $displayText = $event->reason
            ? "Pflanze wurde gelöscht (Grund: {$event->reason})"
            : 'Pflanze wurde gelöscht';

        PlantTimelineProjection::create([
            'plant_uuid' => $event->plantId,
            'event_type' => 'deleted',
            'performed_by' => $event->deletedBy,
            'performed_at' => $event->deletedAt,
            'display_text' => $displayText,
            'event_details' => [
                'reason' => $event->reason,
            ],
            'sequence_number' => $this->getNextSequenceNumber($event->plantId),
        ]);
    }

    /**
     * Handle PlantRestored event - creates timeline entry for plant restoration
     */
    public function onPlantRestored(PlantRestored $event): void
    {
        PlantTimelineProjection::create([
            'plant_uuid' => $event->plantId,
            'event_type' => 'restored',
            'performed_by' => $event->restoredBy,
            'performed_at' => $event->restoredAt,
            'display_text' => 'Pflanze wurde wiederhergestellt',
            'event_details' => [],
            'sequence_number' => $this->getNextSequenceNumber($event->plantId),
        ]);
    }

    /**
     * Get the next sequence number for a plant's timeline
     */
    private function getNextSequenceNumber(string $plantUuid): int
    {
        $lastEntry = PlantTimelineProjection::where('plant_uuid', $plantUuid)
            ->orderByDesc('sequence_number')
            ->first();

        return $lastEntry ? $lastEntry->sequence_number + 1 : 1;
    }

    /**
     * Format field names for display in German
     */
    private function formatFieldsList(array $fields): string
    {
        $fieldTranslations = [
            'name' => 'Name',
            'type' => 'Typ',
            'category' => 'Kategorie',
            'latin_name' => 'Lateinischer Name',
            'description' => 'Beschreibung',
            'image_url' => 'Bild',
        ];

        $translatedFields = array_map(
            fn ($field) => $fieldTranslations[$field] ?? ucfirst($field),
            $fields
        );

        if (count($translatedFields) === 1) {
            return $translatedFields[0];
        }

        if (count($translatedFields) === 2) {
            return implode(' und ', $translatedFields);
        }

        $last = array_pop($translatedFields);

        return implode(', ', $translatedFields).' und '.$last;
    }

    /**
     * Reset projection - delete all timeline entries
     * Used for rebuilding projections from scratch
     */
    public function resetState(): void
    {
        PlantTimelineProjection::truncate();
    }
}
