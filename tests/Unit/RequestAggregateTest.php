<?php

// tests/Unit/PlantAggregateTest.php

use App\Domains\PlantManagement\Aggregates\PlantAggregate;
use App\Domains\PlantManagement\Events\PlantCreated;
use App\Domains\PlantManagement\Events\PlantDeleted;
use App\Domains\PlantManagement\Events\PlantRestored;
use App\Domains\PlantManagement\Events\PlantUpdated;

describe('PlantAggregate - Pure Unit Tests', function () {

    describe('plant creation', function () {

        it('can create a plant with valid data', function () {
            // Arrange - create aggregate instance directly (no container)
            $aggregate = new PlantAggregate;

            // Act
            $aggregate->createPlant(
                name: 'Test Tomato',
                type: 'gemuese',
                category: 'Nightshades',
                latinName: 'Solanum lycopersicum',
                description: 'A delicious red tomato',
                imageUrl: 'https://example.com/tomato.jpg',
                wasUserRequested: true,
                createdBy: 'John Doe' // Custom user
            );

            // Assert - check recorded events directly
            $events = $aggregate->getRecordedEvents();
            expect($events)->toHaveCount(1);

            $event = $events[0];
            expect($event)->toBeInstanceOf(PlantCreated::class)
                ->and($event->name)->toBe('Test Tomato')
                ->and($event->type)->toBe('gemuese')
                ->and($event->category)->toBe('Nightshades')
                ->and($event->latinName)->toBe('Solanum lycopersicum')
                ->and($event->wasUserRequested)->toBeTrue()
                ->and($event->createdBy)->toBe('John Doe'); // User richtig übergeben
        });

        it('can create plant with default system user', function () {
            $aggregate = new PlantAggregate;

            // Act - ohne createdBy Parameter = 'System' default
            $aggregate->createPlant('Test Plant', 'gemuese');

            $events = $aggregate->getRecordedEvents();
            $event = $events[0];

            expect($event->createdBy)->toBe('System'); // Default fallback
        });

        it('validates required plant name', function () {
            $aggregate = new PlantAggregate;

            expect(fn () => $aggregate->createPlant('', 'gemuese'))
                ->toThrow(InvalidArgumentException::class, 'Plant name cannot be empty');
        });

        it('validates plant name length', function () {
            $aggregate = new PlantAggregate;

            expect(fn () => $aggregate->createPlant('A', 'gemuese'))
                ->toThrow(InvalidArgumentException::class, 'Plant name must be at least 2 characters long');

            expect(fn () => $aggregate->createPlant(str_repeat('A', 101), 'gemuese'))
                ->toThrow(InvalidArgumentException::class, 'Plant name cannot exceed 100 characters');
        });

        it('validates plant type', function () {
            $aggregate = new PlantAggregate;

            expect(fn () => $aggregate->createPlant('Test Plant', 'invalid_type'))
                ->toThrow(InvalidArgumentException::class, 'Invalid plant type');
        });

        it('validates latin name format', function () {
            $aggregate = new PlantAggregate;

            expect(fn () => $aggregate->createPlant(
                'Test Plant',
                'gemuese',
                latinName: 'invalid latin name'
            ))->toThrow(InvalidArgumentException::class, 'Latin name should follow "Genus species" format');
        });

        it('validates image URL', function () {
            $aggregate = new PlantAggregate;

            expect(fn () => $aggregate->createPlant(
                'Test Plant',
                'gemuese',
                imageUrl: 'not-a-url'
            ))->toThrow(InvalidArgumentException::class, 'Invalid image URL format');
        });
    });

    describe('plant updates', function () {

        it('can update plant with valid changes', function () {
            // Arrange
            $aggregate = new PlantAggregate;
            $aggregate->createPlant('Original Name', 'gemuese', createdBy: 'Creator');

            // Act
            $aggregate->updatePlant([
                'name' => 'Updated Name',
                'description' => 'New description',
            ], 'Admin User'); // Custom updater

            // Assert
            $events = $aggregate->getRecordedEvents();
            expect($events)->toHaveCount(2);

            $updateEvent = $events[1];
            expect($updateEvent)->toBeInstanceOf(PlantUpdated::class)
                ->and($updateEvent->changes)->toBe([
                    'name' => 'Updated Name',
                    'description' => 'New description',
                ])
                ->and($updateEvent->updatedBy)->toBe('Admin User');
        });

        it('prevents updating deleted plants', function () {
            // Arrange
            $aggregate = new PlantAggregate;
            $aggregate->createPlant('Test Plant', 'gemuese');
            $aggregate->deletePlant(); // Uses default 'System'

            // Act & Assert
            expect(fn () => $aggregate->updatePlant(['name' => 'New Name']))
                ->toThrow(DomainException::class, 'Cannot update deleted plant');
        });

        it('validates update fields', function () {
            $aggregate = new PlantAggregate;
            $aggregate->createPlant('Test Plant', 'gemuese');

            expect(fn () => $aggregate->updatePlant(['invalid_field' => 'value']))
                ->toThrow(InvalidArgumentException::class, 'Invalid fields in update');
        });

        it('filters out non-changes', function () {
            // Arrange
            $aggregate = new PlantAggregate;
            $aggregate->createPlant('Test Plant', 'gemuese');

            // Act & Assert - trying to "update" with same values
            expect(fn () => $aggregate->updatePlant(['name' => 'Test Plant']))
                ->toThrow(DomainException::class, 'No actual changes detected');
        });
    });

    describe('plant deletion and restoration', function () {

        it('can delete a plant', function () {
            // Arrange
            $aggregate = new PlantAggregate;
            $aggregate->createPlant('Test Plant', 'gemuese');

            // Act
            $aggregate->deletePlant('Test reason', 'Admin User'); // Mit Custom User

            // Assert
            $events = $aggregate->getRecordedEvents();
            expect($events)->toHaveCount(2);

            $deleteEvent = $events[1];
            expect($deleteEvent)->toBeInstanceOf(PlantDeleted::class)
                ->and($deleteEvent->reason)->toBe('Test reason')
                ->and($deleteEvent->deletedBy)->toBe('Admin User');
        });

        it('prevents deleting already deleted plants', function () {
            $aggregate = new PlantAggregate;
            $aggregate->createPlant('Test Plant', 'gemuese');
            $aggregate->deletePlant(); // Uses default 'System'

            expect(fn () => $aggregate->deletePlant())
                ->toThrow(DomainException::class, 'Plant is already deleted');
        });

        it('can restore deleted plants', function () {
            // Arrange
            $aggregate = new PlantAggregate;
            $aggregate->createPlant('Test Plant', 'gemuese');
            $aggregate->deletePlant();

            // Act
            $aggregate->restorePlant('Restore Admin'); // Mit Custom User

            // Assert
            $events = $aggregate->getRecordedEvents();
            expect($events)->toHaveCount(3);

            $restoreEvent = $events[2];
            expect($restoreEvent)->toBeInstanceOf(PlantRestored::class)
                ->and($restoreEvent->restoredBy)->toBe('Restore Admin');
        });

        it('prevents restoring non-deleted plants', function () {
            $aggregate = new PlantAggregate;
            $aggregate->createPlant('Test Plant', 'gemuese');

            expect(fn () => $aggregate->restorePlant())
                ->toThrow(DomainException::class, 'Plant is not deleted and cannot be restored');
        });
    });

    describe('state reconstruction', function () {

        it('rebuilds state correctly from events', function () {
            // Arrange - create aggregate and apply events manually
            $aggregate = new PlantAggregate;

            // Apply events in sequence to test state reconstruction
            $aggregate->applyPlantCreated(new PlantCreated(
                plantId: 'test-plant-id',
                name: 'Test Plant',
                type: 'gemuese',
                category: 'Test Category',
                latinName: 'Testus plantus',
                description: 'Test description',
                imageUrl: 'https://example.com/test.jpg',
                createdBy: 'Test User',
                createdAt: now()->toISOString(),
                wasUserRequested: true
            ));

            $aggregate->applyPlantUpdated(new PlantUpdated(
                plantId: 'test-plant-id',
                changes: ['name' => 'Updated Plant', 'description' => 'Updated description'],
                updatedBy: 'Admin',
                updatedAt: now()->toISOString()
            ));

            $aggregate->applyPlantDeleted(new PlantDeleted(
                plantId: 'test-plant-id',
                deletedBy: 'Admin',
                deletedAt: now()->toISOString(),
                reason: 'Test deletion'
            ));

            // Assert state is correctly reconstructed
            expect($aggregate->getName())->toBe('Updated Plant')
                ->and($aggregate->getType())->toBe('gemuese')
                ->and($aggregate->getCategory())->toBe('Test Category')
                ->and($aggregate->getLatinName())->toBe('Testus plantus')
                ->and($aggregate->getDescription())->toBe('Updated description')
                ->and($aggregate->wasUserRequested())->toBeTrue()
                ->and($aggregate->isDeleted())->toBeTrue()
                ->and($aggregate->getCreatedBy())->toBe('Test User')
                ->and($aggregate->getLastUpdatedBy())->toBe('Admin');
        });
    });

    describe('business logic validation', function () {

        it('enforces business rules consistently', function () {
            $aggregate = new PlantAggregate;

            // Test 1: Can create valid plant
            $aggregate->createPlant('Valid Plant', 'gemuese', createdBy: 'Creator');
            expect($aggregate->getName())->toBe('Valid Plant');

            // Test 2: Can update with valid changes
            $aggregate->updatePlant(['description' => 'Added description'], 'Updater');
            expect($aggregate->getDescription())->toBe('Added description');

            // Test 3: Cannot delete already deleted plant
            $aggregate->deletePlant('First deletion', 'Deleter');
            expect($aggregate->isDeleted())->toBeTrue();

            expect(fn () => $aggregate->deletePlant())
                ->toThrow(DomainException::class, 'Plant is already deleted');
        });

        it('validates all plant types correctly', function () {
            $validTypes = ['gemuese', 'obst', 'kraeuter', 'blumen', 'baeume', 'straeucher'];

            foreach ($validTypes as $type) {
                $aggregate = new PlantAggregate;

                // Should not throw exception
                $aggregate->createPlant("Test {$type}", $type);
                expect($aggregate->getType())->toBe($type);
            }
        });
    });
});

// tests/Unit/RequestAggregateTest.php

use App\Domains\RequestManagement\Aggregates\RequestAggregate;
use App\Domains\RequestManagement\Events\PlantCreationRequested;
use App\Domains\RequestManagement\Events\PlantUpdateRequested;
use App\Domains\RequestManagement\Events\RequestApproved;
use App\Domains\RequestManagement\Events\RequestRejected;

describe('RequestAggregate - Pure Unit Tests', function () {

    describe('plant creation requests', function () {

        it('can submit plant creation request with valid data', function () {
            // Arrange
            $aggregate = new RequestAggregate;

            $proposedData = [
                'name' => 'Requested Plant',
                'type' => 'gemuese',
                'category' => 'Test Category',
                'description' => 'User requested plant',
            ];

            // Act
            $aggregate->submitPlantCreationRequest(
                proposedData: $proposedData,
                reason: 'I think this plant would be valuable for the community',
                requestedBy: 'John Doe'
            );

            // Assert
            $events = $aggregate->getRecordedEvents();
            expect($events)->toHaveCount(1);

            $event = $events[0];
            expect($event)->toBeInstanceOf(PlantCreationRequested::class)
                ->and($event->proposedData)->toBe($proposedData)
                ->and($event->reason)->toBe('I think this plant would be valuable for the community')
                ->and($event->requestedBy)->toBe('John Doe');
        });

        it('validates required fields in proposed data', function () {
            $aggregate = new RequestAggregate;

            expect(fn () => $aggregate->submitPlantCreationRequest(
                proposedData: ['type' => 'gemuese'], // missing name
                reason: 'Valid reason that is long enough',
                requestedBy: 'John Doe'
            ))->toThrow(InvalidArgumentException::class, "Field 'name' is required");
        });

        it('validates reason length', function () {
            $aggregate = new RequestAggregate;

            expect(fn () => $aggregate->submitPlantCreationRequest(
                proposedData: ['name' => 'Test', 'type' => 'gemuese'],
                reason: 'Too short', // less than 10 characters
                requestedBy: 'John Doe'
            ))->toThrow(InvalidArgumentException::class, 'Reason must be at least 10 characters long');
        });

        it('validates proposed plant data fields', function () {
            $aggregate = new RequestAggregate;

            expect(fn () => $aggregate->submitPlantCreationRequest(
                proposedData: [
                    'name' => 'Test',
                    'type' => 'invalid_type',
                ],
                reason: 'Valid reason that is long enough',
                requestedBy: 'John Doe'
            ))->toThrow(InvalidArgumentException::class, 'Invalid plant type');
        });
    });

    describe('plant update requests', function () {

        it('can submit update request with valid changes', function () {
            // Arrange
            $aggregate = new RequestAggregate;

            $proposedChanges = [
                'description' => 'Updated description',
                'latin_name' => 'Updatus plantus',
            ];

            // Act
            $aggregate->submitUpdateRequest(
                plantId: '12345678-1234-1234-1234-123456789012',
                proposedChanges: $proposedChanges,
                reason: 'The current information is outdated and needs improvement',
                requestedBy: 'Jane Smith'
            );

            // Assert
            $events = $aggregate->getRecordedEvents();
            expect($events)->toHaveCount(1);

            $event = $events[0];
            expect($event)->toBeInstanceOf(PlantUpdateRequested::class)
                ->and($event->plantId)->toBe('12345678-1234-1234-1234-123456789012')
                ->and($event->proposedChanges)->toBe($proposedChanges)
                ->and($event->reason)->toBe('The current information is outdated and needs improvement');
        });

        it('validates plant ID format', function () {
            $aggregate = new RequestAggregate;

            expect(fn () => $aggregate->submitUpdateRequest(
                plantId: 'invalid-uuid',
                proposedChanges: ['description' => 'test'],
                reason: 'Valid reason that is long enough',
                requestedBy: 'John Doe'
            ))->toThrow(InvalidArgumentException::class, 'Plant ID must be a valid UUID');
        });

        it('validates proposed changes are not empty', function () {
            $aggregate = new RequestAggregate;

            expect(fn () => $aggregate->submitUpdateRequest(
                plantId: '12345678-1234-1234-1234-123456789012',
                proposedChanges: [],
                reason: 'Valid reason that is long enough',
                requestedBy: 'John Doe'
            ))->toThrow(InvalidArgumentException::class, 'Proposed changes cannot be empty');
        });
    });

    describe('request approval workflow', function () {

        it('can approve pending requests', function () {
            // Arrange
            $aggregate = new RequestAggregate;
            $aggregate->submitPlantCreationRequest(
                proposedData: ['name' => 'Test', 'type' => 'gemuese'],
                reason: 'Valid reason that is long enough',
                requestedBy: 'John Doe'
            );

            // Act
            $aggregate->approve('Looks good to me', 'Admin Smith'); // Custom reviewer

            // Assert
            $events = $aggregate->getRecordedEvents();
            expect($events)->toHaveCount(2);

            $approvalEvent = $events[1];
            expect($approvalEvent)->toBeInstanceOf(RequestApproved::class)
                ->and($approvalEvent->comment)->toBe('Looks good to me')
                ->and($approvalEvent->reviewedBy)->toBe('Admin Smith');
        });

        it('can reject pending requests', function () {
            // Arrange
            $aggregate = new RequestAggregate;
            $aggregate->submitPlantCreationRequest(
                proposedData: ['name' => 'Test', 'type' => 'gemuese'],
                reason: 'Valid reason that is long enough',
                requestedBy: 'John Doe'
            );

            // Act
            $aggregate->reject('This plant is not suitable', 'Reviewer Admin');

            // Assert
            $events = $aggregate->getRecordedEvents();
            expect($events)->toHaveCount(2);

            $rejectionEvent = $events[1];
            expect($rejectionEvent)->toBeInstanceOf(RequestRejected::class)
                ->and($rejectionEvent->comment)->toBe('This plant is not suitable')
                ->and($rejectionEvent->reviewedBy)->toBe('Reviewer Admin');
        });

        it('prevents approving non-pending requests', function () {
            $aggregate = new RequestAggregate;
            $aggregate->submitPlantCreationRequest(
                proposedData: ['name' => 'Test', 'type' => 'gemuese'],
                reason: 'Valid reason that is long enough',
                requestedBy: 'John Doe'
            );
            $aggregate->approve(); // Uses default 'System'

            expect(fn () => $aggregate->approve())
                ->toThrow(DomainException::class, 'Only pending requests can be approved');
        });

        it('prevents rejecting non-pending requests', function () {
            $aggregate = new RequestAggregate;
            $aggregate->submitPlantCreationRequest(
                proposedData: ['name' => 'Test', 'type' => 'gemuese'],
                reason: 'Valid reason that is long enough',
                requestedBy: 'John Doe'
            );
            $aggregate->reject('Not suitable'); // Uses default 'System'

            expect(fn () => $aggregate->reject('Another reason'))
                ->toThrow(DomainException::class, 'Only pending requests can be rejected');
        });

        it('requires comment for rejection', function () {
            $aggregate = new RequestAggregate;
            $aggregate->submitPlantCreationRequest(
                proposedData: ['name' => 'Test', 'type' => 'gemuese'],
                reason: 'Valid reason that is long enough',
                requestedBy: 'John Doe'
            );

            expect(fn () => $aggregate->reject(''))
                ->toThrow(InvalidArgumentException::class, 'Comment is required for rejection');
        });
    });

    describe('state reconstruction', function () {

        it('rebuilds state correctly from creation request events', function () {
            // Arrange
            $aggregate = new RequestAggregate;

            // Apply events manually to test state reconstruction
            $aggregate->applyPlantCreationRequested(new PlantCreationRequested(
                requestId: 'test-request-id',
                plantId: 'target-plant-id',
                proposedData: ['name' => 'Test Plant', 'type' => 'gemuese'],
                reason: 'This is a test request',
                requestedBy: 'John Doe',
                requestedAt: now()->toISOString()
            ));

            $aggregate->applyRequestApproved(new RequestApproved(
                requestId: 'test-request-id',
                reviewedBy: 'Admin',
                reviewedAt: now()->addHour()->toISOString(),
                comment: 'Approved'
            ));

            // Assert
            expect($aggregate->getStatus())->toBe('approved')
                ->and($aggregate->getRequestType())->toBe('new_plant')
                ->and($aggregate->getRequestedBy())->toBe('John Doe')
                ->and($aggregate->getPlantId())->toBe('target-plant-id')
                ->and($aggregate->getReason())->toBe('This is a test request')
                ->and($aggregate->getReviewedBy())->toBe('Admin')
                ->and($aggregate->getReviewComment())->toBe('Approved')
                ->and($aggregate->isApproved())->toBeTrue()
                ->and($aggregate->isNewPlantRequest())->toBeTrue();
        });

        it('rebuilds state correctly from update request events', function () {
            // Arrange
            $aggregate = new RequestAggregate;

            // Apply events manually
            $aggregate->applyPlantUpdateRequested(new PlantUpdateRequested(
                requestId: 'test-request-id',
                plantId: 'existing-plant-id',
                proposedChanges: ['description' => 'Updated description'],
                reason: 'Current description is outdated',
                requestedBy: 'Jane Smith',
                requestedAt: now()->toISOString()
            ));

            $aggregate->applyRequestRejected(new RequestRejected(
                requestId: 'test-request-id',
                reviewedBy: 'Admin',
                reviewedAt: now()->addHour()->toISOString(),
                comment: 'Not enough evidence for this change'
            ));

            // Assert
            expect($aggregate->getStatus())->toBe('rejected')
                ->and($aggregate->getRequestType())->toBe('update_contribution')
                ->and($aggregate->getRequestedBy())->toBe('Jane Smith')
                ->and($aggregate->getPlantId())->toBe('existing-plant-id')
                ->and($aggregate->isRejected())->toBeTrue()
                ->and($aggregate->isUpdateRequest())->toBeTrue();
        });
    });

    describe('business rules', function () {

        it('can check if request can be modified', function () {
            $aggregate = new RequestAggregate;
            // Initially cannot be modified (no events yet)
            expect($aggregate->canBeModified())->toBeTrue(); // ✅ Status ist 'pending' = modifiable

            // After submitting request
            $aggregate->submitPlantCreationRequest(
                proposedData: ['name' => 'Test', 'type' => 'gemuese'],
                reason: 'Valid reason that is long enough',
                requestedBy: 'John Doe'
            );
            expect($aggregate->canBeModified())->toBeTrue();

            // After approval (with default 'System')
            $aggregate->approve();
            expect($aggregate->canBeModified())->toBeFalse();
        });

        it('can check if request is ready for review', function () {
            $aggregate = new RequestAggregate;

            expect($aggregate->isReadyForReview())->toBeFalse();

            $aggregate->submitPlantCreationRequest(
                proposedData: ['name' => 'Test', 'type' => 'gemuese'],
                reason: 'Valid reason that is long enough',
                requestedBy: 'John Doe'
            );

            expect($aggregate->isReadyForReview())->toBeTrue();
        });
    });
});
