<?php

use App\Domains\PlantManagement\Events\PlantUpdated;
use Illuminate\Support\Str;

describe('PlantUpdated Event', function () {

    describe('instantiation', function () {

        it('can be created with changes array', function () {
            // Arrange
            $plantId = Str::uuid()->toString();
            $changes = [
                'name' => 'Updated Plant Name',
                'description' => 'Updated description',
                'category' => 'New Category',
            ];
            $updatedAt = now()->toISOString();

            // Act
            $event = new PlantUpdated(
                plantId: $plantId,
                changes: $changes,
                updatedBy: 'Admin User',
                updatedAt: $updatedAt
            );

            // Assert
            expect($event->plantId)->toBe($plantId)
                ->and($event->changes)->toBe($changes)
                ->and($event->updatedBy)->toBe('Admin User')
                ->and($event->updatedAt)->toBe($updatedAt);
        });

        it('can handle empty changes array', function () {
            // Act
            $event = new PlantUpdated(
                plantId: Str::uuid()->toString(),
                changes: [],
                updatedBy: 'Test User',
                updatedAt: now()->toISOString()
            );

            // Assert
            expect($event->changes)
                ->toBe([])
                ->toBeEmpty();
        });
    });

    describe('changes tracking', function () {

        it('preserves all change types', function () {
            // Arrange
            $changes = [
                'name' => 'String change',
                'is_deleted' => true,
                'sequence_number' => 42,
                'tags' => ['tag1', 'tag2'],
                'metadata' => ['key' => 'value'],
            ];

            // Act
            $event = new PlantUpdated(
                plantId: Str::uuid()->toString(),
                changes: $changes,
                updatedBy: 'Test User',
                updatedAt: now()->toISOString()
            );

            // Assert
            expect($event->changes['name'])->toBe('String change')
                ->and($event->changes['is_deleted'])->toBeTrue()
                ->and($event->changes['sequence_number'])->toBe(42)
                ->and($event->changes['tags'])->toBe(['tag1', 'tag2'])
                ->and($event->changes['metadata'])->toBe(['key' => 'value']);
        });

        it('handles null values in changes', function () {
            // Arrange
            $changes = [
                'description' => null,
                'latin_name' => null,
                'image_url' => null,
            ];

            // Act
            $event = new PlantUpdated(
                plantId: Str::uuid()->toString(),
                changes: $changes,
                updatedBy: 'Test User',
                updatedAt: now()->toISOString()
            );

            // Assert
            expect($event->changes['description'])->toBeNull()
                ->and($event->changes['latin_name'])->toBeNull()
                ->and($event->changes['image_url'])->toBeNull();
        });

        it('tracks field-level changes correctly', function () {
            // Arrange
            $changes = [
                'name' => 'Old Name → New Name',
                'category' => 'Wurzelgemüse → Blattgemüse',
            ];

            // Act
            $event = new PlantUpdated(
                plantId: Str::uuid()->toString(),
                changes: $changes,
                updatedBy: 'Admin',
                updatedAt: now()->toISOString()
            );

            // Assert
            expect($event->changes)
                ->toHaveKey('name')
                ->toHaveKey('category')
                ->toHaveCount(2);
        });
    });
});
