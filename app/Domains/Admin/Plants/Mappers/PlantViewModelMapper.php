<?php

// App\Domains\Admin\Plants\Mappers\PlantViewModelMapper.php - Fixed

namespace App\Domains\Admin\Plants\Mappers;

use App\Domains\Admin\Plants\ValueObjects\TimelineEvent;
use App\Domains\Admin\Plants\ViewModels\Show\PlantViewModel;

class PlantViewModelMapper
{
    public function toShowViewModel(array $plantData, array $timelineEvents): PlantViewModel
    {
        // Extrahiere Timeline-Daten für Metadata
        $metadata = $this->extractMetadataFromTimeline($plantData, $timelineEvents);

        return new PlantViewModel(
            uuid: $plantData['uuid'],
            name: $plantData['name'],
            type: $this->mapTypeToDisplay($plantData['type']),
            image_url: $plantData['image_url'] ?? null,

            // Details
            category: $plantData['category'] ?? null,
            latin_name: $plantData['latin_name'] ?? null,
            description: $plantData['description'] ?? null,

            // Metadata aus Timeline extrahiert
            requested_by: $metadata['requested_by'],
            requested_at: $metadata['requested_at'],
            created_by: $metadata['created_by'],
            created_at: $metadata['created_at'],
            updated_by: $metadata['updated_by'],
            updated_at: $metadata['updated_at'],
            deleted_by: $metadata['deleted_by'],
            deleted_at: $metadata['deleted_at'],

            // Timeline Events übergeben
            timelineEvents: $timelineEvents
        );
    }

    public function toIndexViewModel(array $plantData): array
    {
        return [
            'uuid' => $plantData['uuid'],
            'name' => $plantData['name'],
            'type' => $this->mapTypeToDisplay($plantData['type']),
            'category' => $plantData['category'],
            'image_url' => $plantData['image_url'],
            'is_deleted' => $plantData['is_deleted'],
            'was_community_requested' => $plantData['was_community_requested'],
            'last_event_at' => $plantData['last_event_at'],
        ];
    }

    private function extractMetadataFromTimeline(array $plantData, array $timelineEvents): array
    {
        $metadata = [
            'requested_by' => null,
            'requested_at' => null,
            'created_by' => null,
            'created_at' => null,
            'updated_by' => null,
            'updated_at' => null,
            'deleted_by' => null,
            'deleted_at' => null,
        ];

        // Durchlaufe Timeline Events und extrahiere Metadata
        foreach ($timelineEvents as $event) {
            /** @var TimelineEvent $event */
            switch ($event->type) {
                case 'requested':
                    $metadata['requested_by'] = $event->by;
                    $metadata['requested_at'] = $event->at;
                    break;

                case 'created':
                    $metadata['created_by'] = $event->by;
                    $metadata['created_at'] = $event->at;
                    break;

                case 'updated':
                    $metadata['updated_by'] = $event->by;
                    $metadata['updated_at'] = $event->at;
                    break;

                case 'deleted':
                    $metadata['deleted_by'] = $event->by;
                    $metadata['deleted_at'] = $event->at;
                    break;

                case 'restored':
                    // Bei Restore, delete-Info löschen
                    $metadata['deleted_by'] = null;
                    $metadata['deleted_at'] = null;
                    break;
            }
        }

        // Fallback auf Plant-Daten falls Timeline leer
        if (!$metadata['created_at'] && isset($plantData['created_at'])) {
            $metadata['created_by'] = $plantData['created_by'];
            $metadata['created_at'] = $plantData['created_at'];
        }

        return $metadata;
    }

    private function mapTypeToDisplay(string $type): string
    {
        return match ($type) {
            'gemuese' => 'Gemüse',
            'kraeuter' => 'Kräuter',
            'blume' => 'Blumen',
            'strauch' => 'Sträucher',
            'baum' => 'Bäume',
            default => ucfirst($type)
        };
    }
}
