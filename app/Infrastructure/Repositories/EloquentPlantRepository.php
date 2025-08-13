<?php

namespace App\Infrastructure\Repositories;

use App\Domains\Admin\Plants\Contracts\PlantRepositoryInterface;
use App\Models\Plant;
use App\Models\PlantTimelineProjection;

class EloquentPlantRepository implements PlantRepositoryInterface
{
    public function findByUuid(string $uuid): ?array
    {
        $plant = Plant::query()->where('uuid', $uuid)->first();

        return $plant?->toArray();
    }

    public function findWithTimeline(string $uuid): array
    {
        $plant = Plant::where('uuid', $uuid)->firstOrFail();
        $timelineEvents = $this->getTimelineEvents($uuid);

        return [
            'plant' => $plant->toArray(),
            'timeline_events' => $timelineEvents,
        ];
    }

    public function getTimelineEvents(string $plantUuid): array
    {
        return PlantTimelineProjection::where('plant_uuid', $plantUuid)
            ->orderBy('sequence_number')
            ->get()
            ->toArray();
    }

    public function getAll(?array $filters = null): array
    {
        $query = Plant::query()->notDeleted();

        if ($filters) {
            if (isset($filters['type'])) {
                $query->byType($filters['type']);
            }

            if (isset($filters['category'])) {
                $query->byCategory($filters['category']);
            }

            if (isset($filters['search'])) {
                $query->where(function ($q) use ($filters) {
                    $q->where('name', 'like', '%'.$filters['search'].'%')
                        ->orWhere('description', 'like', '%'.$filters['search'].'%')
                        ->orWhere('latin_name', 'like', '%'.$filters['search'].'%');
                });
            }

            if (isset($filters['community_requested']) && $filters['community_requested']) {
                $query->communityRequested();
            }
        }

        return $query->orderBy('last_event_at', 'desc')
            ->get()
            ->toArray();
    }
}
