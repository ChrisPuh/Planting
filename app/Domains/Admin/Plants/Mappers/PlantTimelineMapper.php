<?php

// App\Domains\Admin\Plants\Mappers\PlantTimelineMapper.php - Clean Architecture Refactor

namespace App\Domains\Admin\Plants\Mappers;

use App\Domains\Admin\Plants\ValueObjects\TimelineEvent;
use Illuminate\Support\Carbon;

class PlantTimelineMapper
{
    /**
     * Mappt Timeline Events aus der DB zu TimelineEvent ValueObjects
     *
     * @param array $timelineEvents
     * @param bool $isAdmin - Admin-Status als Parameter statt auth() Facade
     */
    public function mapTimelineEventsFromDatabase(array $timelineEvents, bool $isAdmin = false): array
    {
        return array_map(function ($event) use ($isAdmin) {
            return $this->mapSingleEvent($event, $isAdmin);
        }, $timelineEvents);
    }

    private function mapSingleEvent(array $event, bool $isAdmin): TimelineEvent
    {
        return match ($event['event_type']) {
            'requested' => TimelineEvent::requested(
                $event['performed_by'],
                $this->formatDate($event['performed_at']),
                $isAdmin
            ),
            'created' => TimelineEvent::created(
                $event['performed_by'],
                $this->formatDate($event['performed_at']),
                true // Created events sind immer sichtbar
            ),
            'updated' => TimelineEvent::updated(
                $event['performed_by'],
                $this->formatDate($event['performed_at']),
                true, // Update events sind immer sichtbar
                $this->extractChangedFields($event)
            ),
            'update_requested' => TimelineEvent::updateRequested(
                $event['performed_by'],
                $this->formatDate($event['performed_at']),
                $isAdmin,
                $this->extractRequestedFields($event)
            ),
            'deleted' => TimelineEvent::deleted(
                $event['performed_by'],
                $this->formatDate($event['performed_at']),
                $isAdmin
            ),
            'restored' => TimelineEvent::restored(
                $event['performed_by'],
                $this->formatDate($event['performed_at']),
                $isAdmin
            ),
            default => throw new \InvalidArgumentException("Unknown timeline event type: {$event['event_type']}")
        };
    }

    private function extractChangedFields(array $event): ?array
    {
        $eventDetails = $event['event_details'] ?? [];

        return $eventDetails['changed_fields'] ?? null;
    }

    private function extractRequestedFields(array $event): ?array
    {
        $eventDetails = $event['event_details'] ?? [];

        return $eventDetails['requested_fields'] ?? null;
    }

    private function formatDate($date): string
    {
        if ($date instanceof Carbon) {
            return $date->format('d.m.Y H:i');
        }

        return Carbon::parse($date)->format('d.m.Y H:i');
    }

    /**
     * Dummy-Events für Development/Testing (fallback)
     *
     * @param array $plantData
     * @param bool $isAdmin - Admin-Status als Parameter
     */
    public function createDummyTimelineEvents(array $plantData, bool $isAdmin = false): array
    {
        $createdAt = Carbon::parse($plantData['created_at']);

        $dummyEvents = [
            [
                'event_type' => 'created',
                'performed_by' => $plantData['created_by'] ?? 'Admin_User',
                'performed_at' => $createdAt,
                'event_details' => [],
            ],
        ];

        // Wenn Community requested
        if ($plantData['was_community_requested'] ?? false) {
            array_unshift($dummyEvents, [
                'event_type' => 'requested',
                'performed_by' => 'Community_User',
                'performed_at' => $createdAt->copy()->subDays(5),
                'event_details' => [],
            ]);
        }

        // Paar Dummy Updates
        $dummyEvents[] = [
            'event_type' => 'update_requested',
            'performed_by' => 'Botaniker_Bob',
            'performed_at' => $createdAt->copy()->addDays(2),
            'event_details' => ['requested_fields' => ['description']],
        ];

        $dummyEvents[] = [
            'event_type' => 'updated',
            'performed_by' => 'Admin_User',
            'performed_at' => $createdAt->copy()->addDays(3),
            'event_details' => ['changed_fields' => ['description']],
        ];

        return $this->mapTimelineEventsFromDatabase($dummyEvents, $isAdmin);
    }
}
