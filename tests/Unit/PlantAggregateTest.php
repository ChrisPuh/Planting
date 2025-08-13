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
