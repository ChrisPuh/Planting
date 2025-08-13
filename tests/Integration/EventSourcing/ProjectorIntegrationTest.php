<?php

use App\Domains\PlantManagement\Events\PlantCreated;
use App\Domains\PlantManagement\Events\PlantDeleted;
use App\Domains\PlantManagement\Events\PlantRestored;
use App\Domains\PlantManagement\Events\PlantUpdated;
use App\Domains\RequestManagement\Events\PlantCreationRequested;
use App\Domains\RequestManagement\Events\PlantUpdateRequested;
use App\Domains\RequestManagement\Events\RequestApproved;
use App\Domains\RequestManagement\Events\RequestRejected;
use App\Models\Plant;
use App\Models\PlantTimelineProjection;
use App\Models\RequestQueueProjection;
use Illuminate\Support\Str;

describe('Projector Integration Tests', function () {

    beforeEach(function () {
        $this->plantId = Str::uuid()->toString();
        $this->requestId = Str::uuid()->toString();
    });

    describe('PlantProjector', function () {

        it('creates plant from PlantCreated event', function () {
            // Arrange
            $event = new PlantCreated(
                plantId: $this->plantId,
                name: 'Test Tomato',
                type: 'gemuese',
                category: 'Fruchtgemüse',
                latinName: 'Solanum lycopersicum',
                description: 'A delicious red tomato',
                imageUrl: 'https://example.com/tomato.jpg',
                createdBy: 'Test User',
                createdAt: now()->toISOString(),
                wasUserRequested: true
            );

            // Act
            event($event);

            // Assert
            expect(Plant::where('uuid', $this->plantId)->first())
                ->not->toBeNull()
                ->name->toBe('Test Tomato')
                ->type->toBe('gemuese')
                ->category->toBe('Fruchtgemüse')
                ->latin_name->toBe('Solanum lycopersicum')
                ->description->toBe('A delicious red tomato')
                ->image_url->toBe('https://example.com/tomato.jpg')
                ->is_deleted->toBeFalse()
                ->was_community_requested->toBeTrue()
                ->created_by->toBe('Test User')
                ->last_updated_by->toBe('Test User')
                ->is_active->toBeTrue();
        });

        it('updates plant from PlantUpdated event', function () {
            // Arrange - Create initial plant
            event(new PlantCreated(
                plantId: $this->plantId,
                name: 'Original Name',
                type: 'gemuese',
                category: null,
                latinName: null,
                description: null,
                imageUrl: null,
                createdBy: 'Test User',
                createdAt: now()->toISOString()
            ));

            $updateEvent = new PlantUpdated(
                plantId: $this->plantId,
                changes: [
                    'name' => 'Updated Tomato',
                    'description' => 'Now with description',
                    'category' => 'Fruchtgemüse'
                ],
                updatedBy: 'Admin User',
                updatedAt: now()->addMinutes(5)->toISOString()
            );

            // Act
            event($updateEvent);

            // Assert
            $plant = Plant::where('uuid', $this->plantId)->first();
            expect($plant)
                ->name->toBe('Updated Tomato')
                ->description->toBe('Now with description')
                ->category->toBe('Fruchtgemüse')
                ->last_updated_by->toBe('Admin User');
        });

        it('soft deletes plant from PlantDeleted event', function () {
            // Arrange
            event(new PlantCreated(
                plantId: $this->plantId,
                name: 'Test Plant',
                type: 'gemuese',
                category: null,
                latinName: null,
                description: null,
                imageUrl: null,
                createdBy: 'Test User',
                createdAt: now()->toISOString()
            ));

            $deleteEvent = new PlantDeleted(
                plantId: $this->plantId,
                deletedBy: 'Admin',
                deletedAt: now()->addHour()->toISOString(),
                reason: 'Test deletion'
            );

            // Act
            event($deleteEvent);

            // Assert
            $plant = Plant::where('uuid', $this->plantId)->first();
            expect($plant)
                ->is_deleted->toBeTrue()
                ->last_updated_by->toBe('Admin')
                ->is_active->toBeFalse();
        });

        it('restores plant from PlantRestored event', function () {
            // Arrange - Create and delete plant
            event(new PlantCreated(
                plantId: $this->plantId,
                name: 'Test Plant',
                type: 'gemuese',
                category: null,
                latinName: null,
                description: null,
                imageUrl: null,
                createdBy: 'Test User',
                createdAt: now()->toISOString()
            ));

            event(new PlantDeleted(
                plantId: $this->plantId,
                deletedBy: 'Admin',
                deletedAt: now()->addHour()->toISOString()
            ));

            $restoreEvent = new PlantRestored(
                plantId: $this->plantId,
                restoredBy: 'Super Admin',
                restoredAt: now()->addHours(2)->toISOString()
            );

            // Act
            event($restoreEvent);

            // Assert
            $plant = Plant::where('uuid', $this->plantId)->first();
            expect($plant)
                ->is_deleted->toBeFalse()
                ->last_updated_by->toBe('Super Admin')
                ->is_active->toBeTrue();
        });
    });

    describe('PlantTimelineProjector', function () {

        it('creates timeline entry for PlantCreated event', function () {
            // Arrange
            $event = new PlantCreated(
                plantId: $this->plantId,
                name: 'Timeline Test Plant',
                type: 'blume',
                category: null,
                latinName: null,
                description: null,
                imageUrl: null,
                createdBy: 'Timeline User',
                createdAt: now()->toISOString(),
                wasUserRequested: true
            );

            // Act
            event($event);

            // Assert
            $timeline = PlantTimelineProjection::where('plant_uuid', $this->plantId)->first();
            expect($timeline)
                ->not->toBeNull()
                ->plant_uuid->toBe($this->plantId)
                ->event_type->toBe('created')
                ->performed_by->toBe('Timeline User')
                ->sequence_number->toBe(1)
                ->display_text->toContain('Timeline Test Plant')
                ->display_text->toContain('Community-Anfrage')
                ->event_details->toHaveKey('was_user_requested')
                ->event_details->toHaveKey('initial_data');

            expect($timeline->event_details['was_user_requested'])->toBeTrue();
        });

        it('creates timeline entry for PlantUpdated event', function () {
            // Arrange - Create plant first
            event(new PlantCreated(
                plantId: $this->plantId,
                name: 'Original Plant',
                type: 'gemuese',
                category: null,
                latinName: null,
                description: null,
                imageUrl: null,
                createdBy: 'User',
                createdAt: now()->toISOString()
            ));

            $updateEvent = new PlantUpdated(
                plantId: $this->plantId,
                changes: [
                    'name' => 'Updated Plant',
                    'description' => 'Added description'
                ],
                updatedBy: 'Editor',
                updatedAt: now()->addMinutes(10)->toISOString()
            );

            // Act
            event($updateEvent);

            // Assert
            $timeline = PlantTimelineProjection::where('plant_uuid', $this->plantId)
                ->where('event_type', 'updated')
                ->first();

            expect($timeline)
                ->not->toBeNull()
                ->sequence_number->toBe(2)
                ->performed_by->toBe('Editor')
                ->display_text->toContain('Name und Beschreibung');

            expect($timeline->event_details['changed_fields'])->toBe(['name', 'description']);
        });

        it('creates timeline entry for PlantDeleted event', function () {
            // Arrange
            event(new PlantCreated(
                plantId: $this->plantId,
                name: 'Test Plant',
                type: 'gemuese',
                category: null,
                latinName: null,
                description: null,
                imageUrl: null,
                createdBy: 'User',
                createdAt: now()->toISOString()
            ));

            $deleteEvent = new PlantDeleted(
                plantId: $this->plantId,
                deletedBy: 'Admin',
                deletedAt: now()->addHour()->toISOString(),
                reason: 'Test completion'
            );

            // Act
            event($deleteEvent);

            // Assert
            $timeline = PlantTimelineProjection::where('plant_uuid', $this->plantId)
                ->where('event_type', 'deleted')
                ->first();

            expect($timeline)
                ->not->toBeNull()
                ->sequence_number->toBe(2)
                ->performed_by->toBe('Admin')
                ->display_text->toContain('Grund: Test completion');

            expect($timeline->event_details['reason'])->toBe('Test completion');
        });

        it('creates timeline entry for PlantCreationRequested event even without existing plant', function () {
            // Arrange - No plant exists yet, but timeline should still work
            $event = new PlantCreationRequested(
                requestId: $this->requestId,
                plantId: $this->plantId,
                proposedData: [
                    'name' => 'Requested Plant',
                    'type' => 'kraeuter'
                ],
                reason: 'Testing request timeline',
                requestedBy: 'Community User',
                requestedAt: now()->toISOString()
            );

            // Act
            event($event);

            // Assert - Timeline entry should exist even without plant
            $timeline = PlantTimelineProjection::where('plant_uuid', $this->plantId)->first();
            expect($timeline)
                ->not->toBeNull()
                ->event_type->toBe('requested')
                ->performed_by->toBe('Community User')
                ->sequence_number->toBe(1)
                ->display_text->toBe('Neue Pflanze wurde beantragt');
        });
    });

    describe('RequestQueueProjector', function () {

        it('creates entry for PlantCreationRequested event', function () {
            // Arrange
            $event = new PlantCreationRequested(
                requestId: $this->requestId,
                plantId: $this->plantId,
                proposedData: [
                    'name' => 'Community Requested Plant',
                    'type' => 'kraeuter',
                    'description' => 'This plant was requested by the community'
                ],
                reason: 'We need more herbs in our database',
                requestedBy: 'Community Member',
                requestedAt: now()->toISOString()
            );

            // Act
            event($event);

            // Assert
            $request = RequestQueueProjection::where('uuid', $this->requestId)->first();
            expect($request)
                ->not->toBeNull()
                ->uuid->toBe($this->requestId)
                ->plant_uuid->toBe($this->plantId)
                ->request_type->toBe('new_plant')
                ->requested_by->toBe('Community Member')
                ->status->toBe('pending')
                ->reason->toBe('We need more herbs in our database')
                ->is_new_plant_request->toBeTrue()
                ->is_pending->toBeTrue()
                ->proposed_plant_name->toBe('Community Requested Plant')
                ->status_display->toBe('Ausstehend')
                ->request_type_display->toBe('Neue Pflanze');
        });

        it('creates entry for PlantUpdateRequested event', function () {
            // Arrange
            $event = new PlantUpdateRequested(
                requestId: $this->requestId,
                plantId: $this->plantId,
                proposedChanges: [
                    'description' => 'Updated description',
                    'latin_name' => 'Botanicus updateus'
                ],
                reason: 'Current information is outdated',
                requestedBy: 'Expert User',
                requestedAt: now()->toISOString()
            );

            // Act
            event($event);

            // Assert
            $request = RequestQueueProjection::where('uuid', $this->requestId)->first();
            expect($request)
                ->not->toBeNull()
                ->request_type->toBe('update_contribution')
                ->requested_by->toBe('Expert User')
                ->status->toBe('pending')
                ->is_update_request->toBeTrue()
                ->proposed_fields_display->toBe('Beschreibung und Lateinischer Name');

            expect($request->proposed_fields)->toBe(['description', 'latin_name']);
        });

        it('updates status when request is approved', function () {
            // Arrange - Create request first
            event(new PlantCreationRequested(
                requestId: $this->requestId,
                plantId: $this->plantId,
                proposedData: ['name' => 'Test', 'type' => 'gemuese'],
                reason: 'Test reason',
                requestedBy: 'User',
                requestedAt: now()->toISOString()
            ));

            $approvalEvent = new RequestApproved(
                requestId: $this->requestId,
                reviewedBy: 'Admin Reviewer',
                reviewedAt: now()->addDay()->toISOString(),
                comment: 'Great suggestion!'
            );

            // Act
            event($approvalEvent);

            // Assert
            $request = RequestQueueProjection::where('uuid', $this->requestId)->first();
            expect($request)
                ->status->toBe('approved')
                ->reviewed_by->toBe('Admin Reviewer')
                ->admin_comment->toBe('Great suggestion!')
                ->is_approved->toBeTrue()
                ->is_reviewed->toBeTrue()
                ->status_display->toBe('Genehmigt');
        });

        it('updates status when request is rejected', function () {
            // Arrange - Create request first
            event(new PlantUpdateRequested(
                requestId: $this->requestId,
                plantId: $this->plantId,
                proposedChanges: ['description' => 'Bad change'],
                reason: 'Not good reason',
                requestedBy: 'User',
                requestedAt: now()->toISOString()
            ));

            $rejectionEvent = new RequestRejected(
                requestId: $this->requestId,
                reviewedBy: 'Admin Reviewer',
                reviewedAt: now()->addDay()->toISOString(),
                comment: 'Insufficient evidence for this change'
            );

            // Act
            event($rejectionEvent);

            // Assert
            $request = RequestQueueProjection::where('uuid', $this->requestId)->first();
            expect($request)
                ->status->toBe('rejected')
                ->reviewed_by->toBe('Admin Reviewer')
                ->admin_comment->toBe('Insufficient evidence for this change')
                ->is_rejected->toBeTrue()
                ->status_display->toBe('Abgelehnt');
        });
    });

    describe('complete workflow integration', function () {

        it('processes complete request-to-restoration workflow correctly', function () {
            // Arrange - Complete workflow: Request -> Approval -> Creation -> Update -> Deletion -> Restoration

            // 1. Plant Creation Request
            event(new PlantCreationRequested(
                requestId: $this->requestId,
                plantId: $this->plantId,
                proposedData: ['name' => 'Workflow Plant', 'type' => 'gemuese'],
                reason: 'Testing complete workflow',
                requestedBy: 'Workflow User',
                requestedAt: now()->toISOString()
            ));

            // 2. Request Approval
            event(new RequestApproved(
                requestId: $this->requestId,
                reviewedBy: 'Admin',
                reviewedAt: now()->addMinutes(30)->toISOString(),
                comment: 'Approved for testing'
            ));

            // 3. Plant Creation (after approval)
            event(new PlantCreated(
                plantId: $this->plantId,
                name: 'Workflow Plant',
                type: 'gemuese',
                category: null,
                latinName: null,
                description: null,
                imageUrl: null,
                createdBy: 'Admin',
                createdAt: now()->addHour()->toISOString(),
                wasUserRequested: true
            ));

            // 4. Plant Update
            event(new PlantUpdated(
                plantId: $this->plantId,
                changes: ['description' => 'Added by workflow'],
                updatedBy: 'Editor',
                updatedAt: now()->addHours(2)->toISOString()
            ));

            // 5. Plant Deletion
            event(new PlantDeleted(
                plantId: $this->plantId,
                deletedBy: 'Admin',
                deletedAt: now()->addHours(3)->toISOString(),
                reason: 'Workflow test complete'
            ));

            // 6. Plant Restoration
            event(new PlantRestored(
                plantId: $this->plantId,
                restoredBy: 'Super Admin',
                restoredAt: now()->addHours(4)->toISOString()
            ));

            // Assert - Verify final plant state
            $plant = Plant::where('uuid', $this->plantId)->first();
            expect($plant)
                ->name->toBe('Workflow Plant')
                ->description->toBe('Added by workflow')
                ->is_deleted->toBeFalse()
                ->was_community_requested->toBeTrue();

            // Verify request state
            $request = RequestQueueProjection::where('uuid', $this->requestId)->first();
            expect($request)
                ->status->toBe('approved')
                ->admin_comment->toBe('Approved for testing');

            // Check timeline sequence (should have 5 events: requested, created, updated, deleted, restored)
            $timelineEvents = PlantTimelineProjection::where('plant_uuid', $this->plantId)
                ->orderBy('sequence_number')
                ->get();

            expect($timelineEvents)->toHaveCount(5);

            // Verify event types in correct order
            expect($timelineEvents[0]->event_type)->toBe('requested');
            expect($timelineEvents[1]->event_type)->toBe('created');
            expect($timelineEvents[2]->event_type)->toBe('updated');
            expect($timelineEvents[3]->event_type)->toBe('deleted');
            expect($timelineEvents[4]->event_type)->toBe('restored');

            // Verify sequence numbers are correct
            $timelineEvents->each(function ($event, $index) {
                expect($event->sequence_number)->toBe($index + 1);
            });
        });
    });
});
