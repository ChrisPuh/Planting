<?php

use App\Domains\PlantManagement\Events\PlantCreated;
use App\Domains\PlantManagement\Events\PlantDeleted;
use App\Domains\PlantManagement\Events\PlantUpdated;
use Spatie\EventSourcing\StoredEvents\Models\EloquentStoredEvent;

describe('Plant Event Workflow', function () {

    describe('complete lifecycle', function () {

        it('handles full plant lifecycle through events', function () {
            // This test verifies the entire event flow
            // from creation through updates to deletion

            $plantId = Str::uuid()->toString();

            // Act - Trigger complete lifecycle

            // 1. Plant Created
            event(new PlantCreated(
                plantId: $plantId,
                name: 'Lifecycle Test Plant',
                type: 'gemuese',
                category: 'Test Category',
                latinName: null,
                description: null,
                imageUrl: null,
                createdBy: 'Test User',
                createdAt: now()->toISOString()
            ));

            // 2. Plant Updated (description added)
            event(new PlantUpdated(
                plantId: $plantId,
                changes: ['description' => 'Added description during lifecycle'],
                updatedBy: 'Admin',
                updatedAt: now()->addMinutes(5)->toISOString()
            ));

            // 3. Plant Updated (category changed)
            event(new PlantUpdated(
                plantId: $plantId,
                changes: ['category' => 'Updated Category'],
                updatedBy: 'Expert User',
                updatedAt: now()->addMinutes(10)->toISOString()
            ));

            // 4. Plant Deleted
            event(new PlantDeleted(
                plantId: $plantId,
                deletedBy: 'Admin',
                deletedAt: now()->addMinutes(15)->toISOString(),
                reason: 'Lifecycle test completion'
            ));

            // Assert - Complete workflow verification
            $events = EloquentStoredEvent::orderBy('id')->get();
            expect($events)->toHaveCount(4)
                ->and($events[0]->event_class)->toBe(PlantCreated::class)
                ->and($events[1]->event_class)->toBe(PlantUpdated::class)
                ->and($events[2]->event_class)->toBe(PlantUpdated::class)
                ->and($events[3]->event_class)->toBe(PlantDeleted::class);

            // Verify event sequence

            // Verify all events belong to same aggregate
            $events->each(function ($event) use ($plantId) {
                expect($event->event_properties['plantId'])->toBe($plantId);
            });

            // Verify event progression
            expect($events[1]->event_properties['changes']['description'])
                ->toBe('Added description during lifecycle')
                ->and($events[2]->event_properties['changes']['category'])
                ->toBe('Updated Category')
                ->and($events[3]->event_properties['reason'])
                ->toBe('Lifecycle test completion');
        });
    });
});
