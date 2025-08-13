<?php

use App\Domains\PlantManagement\Events\PlantCreated;
use Illuminate\Support\Str;

describe('PlantCreated Event', function () {

    describe('instantiation', function () {

        it('can be created with all required fields', function () {
            // Arrange
            $plantId = Str::uuid()->toString();
            $createdAt = now()->toISOString();

            // Act
            $event = new PlantCreated(
                plantId: $plantId,
                name: 'Test Plant',
                type: 'gemuese',
                category: 'Wurzelgemüse',
                latinName: 'Testus plantus',
                description: 'Test description',
                imageUrl: 'https://example.com/test.jpg',
                createdBy: 'Test User',
                createdAt: $createdAt,
                wasUserRequested: false
            );

            // Assert
            expect($event->plantId)->toBe($plantId)
                ->and($event->name)->toBe('Test Plant')
                ->and($event->type)->toBe('gemuese')
                ->and($event->category)->toBe('Wurzelgemüse')
                ->and($event->latinName)->toBe('Testus plantus')
                ->and($event->description)->toBe('Test description')
                ->and($event->imageUrl)->toBe('https://example.com/test.jpg')
                ->and($event->createdBy)->toBe('Test User')
                ->and($event->createdAt)->toBe($createdAt)
                ->and($event->wasUserRequested)->toBeFalse();
        });

        it('can be created with nullable fields as null', function () {
            // Act
            $event = new PlantCreated(
                plantId: Str::uuid()->toString(),
                name: 'Simple Plant',
                type: 'blume',
                category: null,
                latinName: null,
                description: null,
                imageUrl: null,
                createdBy: 'Admin',
                createdAt: now()->toISOString(),
                wasUserRequested: true
            );

            // Assert
            expect($event->category)->toBeNull()
                ->and($event->latinName)->toBeNull()
                ->and($event->description)->toBeNull()
                ->and($event->imageUrl)->toBeNull()
                ->and($event->wasUserRequested)->toBeTrue();
        });

        it('handles UUID format correctly', function () {
            // Arrange
            $plantId = Str::uuid()->toString();

            // Act
            $event = new PlantCreated(
                plantId: $plantId,
                name: 'UUID Test',
                type: 'gemuese',
                category: null,
                latinName: null,
                description: null,
                imageUrl: null,
                createdBy: 'Test User',
                createdAt: now()->toISOString()
            );

            // Assert
            expect($event->plantId)
                ->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/')
                ->and(Str::isUuid($event->plantId))->toBeTrue();
        });
    });

    describe('properties', function () {

        it('has readonly properties', function () {
            // Arrange
            $event = new PlantCreated(
                plantId: Str::uuid()->toString(),
                name: 'Test Plant',
                type: 'gemuese',
                category: null,
                latinName: null,
                description: null,
                imageUrl: null,
                createdBy: 'Test User',
                createdAt: now()->toISOString()
            );

            // Assert - Properties sind readonly (PHP 8.1+)
            expect(fn () => $event->name = 'Modified Name')
                ->toThrow(Error::class, 'Cannot modify readonly property');
        });

        it('handles special characters in names', function () {
            // Arrange
            $specialName = 'Rote Beete (Beta vulgaris) - Süßkartoffel #1 & Co. 🌱';

            // Act
            $event = new PlantCreated(
                plantId: Str::uuid()->toString(),
                name: $specialName,
                type: 'gemuese',
                category: null,
                latinName: null,
                description: null,
                imageUrl: null,
                createdBy: 'Test User',
                createdAt: now()->toISOString()
            );

            // Assert
            expect($event->name)->toBe($specialName)
                ->and(str_contains($event->name, 'ß'))->toBeTrue()
                ->and(str_contains($event->name, '🌱'))->toBeTrue();
        });
    });

    describe('edge cases', function () {

        it('handles long plant names correctly', function () {
            // Arrange
            $longName = str_repeat('Very Long Plant Name ', 10);

            // Act
            $event = new PlantCreated(
                plantId: Str::uuid()->toString(),
                name: $longName,
                type: 'gemuese',
                category: null,
                latinName: null,
                description: null,
                imageUrl: null,
                createdBy: 'Test User',
                createdAt: now()->toISOString()
            );

            // Assert
            expect($event->name)->toBe($longName)
                ->and(strlen($event->name))->toBeGreaterThan(200);
        });

        it('preserves ISO date format', function () {
            // Arrange
            $isoDate = '2025-01-15T14:30:00.000000Z';

            // Act
            $event = new PlantCreated(
                plantId: Str::uuid()->toString(),
                name: 'Date Test Plant',
                type: 'gemuese',
                category: null,
                latinName: null,
                description: null,
                imageUrl: null,
                createdBy: 'Test User',
                createdAt: $isoDate
            );

            // Assert
            expect($event->createdAt)->toBe($isoDate)
                ->and($event->createdAt)->toMatch('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/');
        });
    });
});
