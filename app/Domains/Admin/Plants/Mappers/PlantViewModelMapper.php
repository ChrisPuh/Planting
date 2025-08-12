<?php

namespace App\Domains\Admin\Plants\Mappers;

use App\Domains\Admin\Plants\ViewModels\Show\PlantViewModel;
use App\Domains\Admin\Plants\ValueObjects\TimelineEvent;
use Illuminate\Support\Carbon;

class PlantViewModelMapper
{
    public function toShowViewModel(array $plantData, array $timelineData = []): PlantViewModel
    {
        return new PlantViewModel(
            id: $plantData['id'],
            name: $plantData['name'],
            type: $plantData['type'],
            image_url: $plantData['image_url'] ?? null,
            category: $plantData['category'] ?? null,
            latin_name: $plantData['latin_name'] ?? null,
            description: $plantData['description'] ?? null,

            requested_by: $plantData['requested_by'] ?? null,
            requested_at: $plantData['requested_at'],
            created_by: $plantData['created_by'] ?? null,
            created_at: $plantData['created_at'],
            updated_by: $plantData['updated_by'] ?? null,
            updated_at: $plantData['updated_at'] ?? null,
            deleted_by: $plantData['deleted_by'] ?? null,
            deleted_at: $plantData['deleted_at'] ?? null,

            timelineEvents: $this->mapTimelineEvents($timelineData)
        );
    }

    /**
     * @param array $timelineData
     * @return TimelineEvent[]
     */
    private function mapTimelineEvents(array $timelineData): array
    {
        return array_map(function ($event) {
            return match ($event['type']) {
                'requested' => TimelineEvent::requested(
                    $event['by'],
                    $this->formatDate($event['at']),
                    $event['show_by'] ?? false
                ),
                'created' => TimelineEvent::created(
                    $event['by'],
                    $this->formatDate($event['at']),
                    $event['show_by'] ?? true
                ),
                'updated' => TimelineEvent::updated(
                    $event['by'],
                    $this->formatDate($event['at']),
                    $event['show_by'] ?? true,
                    $event['details'] ?? null
                ),
                'update_requested' => TimelineEvent::updateRequested(
                    $event['by'],
                    $this->formatDate($event['at']),
                    $event['show_by'] ?? false,
                    $event['details'] ?? null
                ),
                'deleted' => TimelineEvent::deleted(
                    $event['by'],
                    $this->formatDate($event['at']),
                    $event['show_by'] ?? true
                ),
                'restored' => TimelineEvent::restored(
                    $event['by'],
                    $this->formatDate($event['at']),
                    $event['show_by'] ?? true
                ),
                default => throw new \InvalidArgumentException("Unknown timeline event type: {$event['type']}")
            };
        }, $timelineData);
    }

    private function formatDate($date): ?string
    {
        if (!$date) return null;

        return $date instanceof Carbon
            ? $date->format('d.m.Y H:i')
            : Carbon::parse($date)->format('d.m.Y H:i');
    }
}
