<?php

use App\Domains\RequestManagement\Events\PlantCreationRequested;
use Illuminate\Support\Str;

describe('PlantCreationRequested Event', function () {

    describe('instantiation', function () {

        it('can be created with all request data', function () {
            // Arrange
            $requestId = Str::uuid()->toString();
            $plantId = Str::uuid()->toString();
            $proposedData = [
                'name' => 'Community Requested Plant',
                'type' => 'gemuese',
                'category' => 'Wurzelgemüse',
                'description' => 'This plant was requested by the community',
            ];
            $reason = 'This plant is missing from our database but commonly grown';
            $requestedAt = now()->toISOString();

            // Act
            $event = new PlantCreationRequested(
                requestId: $requestId,
                plantId: $plantId,
                proposedData: $proposedData,
                reason: $reason,
                requestedBy: 'Community User',
                requestedAt: $requestedAt
            );

            // Assert
            expect($event->requestId)->toBe($requestId)
                ->and($event->plantId)->toBe($plantId)
                ->and($event->proposedData)->toBe($proposedData)
                ->and($event->reason)->toBe($reason)
                ->and($event->requestedBy)->toBe('Community User')
                ->and($event->requestedAt)->toBe($requestedAt);
        });
    });

    describe('proposed data validation', function () {

        it('handles minimal proposed data', function () {
            // Arrange
            $minimalData = [
                'name' => 'Simple Plant',
                'type' => 'blume',
            ];

            // Act
            $event = new PlantCreationRequested(
                requestId: Str::uuid()->toString(),
                plantId: Str::uuid()->toString(),
                proposedData: $minimalData,
                reason: 'Minimal test',
                requestedBy: 'Test User',
                requestedAt: now()->toISOString()
            );

            // Assert
            expect($event->proposedData)->toBe($minimalData)
                ->and($event->proposedData['name'])->toBe('Simple Plant')
                ->and($event->proposedData['type'])->toBe('blume')
                ->and($event->proposedData)->toHaveCount(2);
        });

        it('handles comprehensive proposed data', function () {
            // Arrange
            $comprehensiveData = [
                'name' => 'Comprehensive Plant',
                'type' => 'kraeuter',
                'category' => 'Kräuter',
                'latin_name' => 'Comprehensis plantus',
                'description' => 'A very detailed plant description',
                'image_url' => 'https://example.com/plant.jpg',
                'care_instructions' => 'Water daily, partial shade',
                'growth_season' => 'Spring to Fall',
                'mature_size' => '30-50cm',
            ];

            // Act
            $event = new PlantCreationRequested(
                requestId: Str::uuid()->toString(),
                plantId: Str::uuid()->toString(),
                proposedData: $comprehensiveData,
                reason: 'Complete test',
                requestedBy: 'Expert User',
                requestedAt: now()->toISOString()
            );

            // Assert
            expect($event->proposedData)->toBe($comprehensiveData)
                ->and($event->proposedData)->toHaveKey('name')
                ->and($event->proposedData)->toHaveKey('latin_name')
                ->and($event->proposedData)->toHaveKey('care_instructions')
                ->and($event->proposedData)->toHaveCount(9);
        });

        it('preserves complex data structures', function () {
            // Arrange
            $complexData = [
                'name' => 'Complex Plant',
                'type' => 'gemuese',
                'growing_tips' => [
                    'watering' => ['frequency' => 'daily', 'amount' => '200ml'],
                    'soil' => ['type' => 'well-draining', 'ph' => 6.5],
                    'climate' => ['min_temp' => 15, 'max_temp' => 25],
                ],
                'companion_plants' => ['tomatoes', 'basil', 'carrots'],
            ];

            // Act
            $event = new PlantCreationRequested(
                requestId: Str::uuid()->toString(),
                plantId: Str::uuid()->toString(),
                proposedData: $complexData,
                reason: 'Complex structure test',
                requestedBy: 'Advanced User',
                requestedAt: now()->toISOString()
            );

            // Assert
            expect($event->proposedData['growing_tips']['watering']['frequency'])->toBe('daily')
                ->and($event->proposedData['growing_tips']['soil']['ph'])->toBe(6.5)
                ->and($event->proposedData['companion_plants'])->toContain('tomatoes')
                ->and($event->proposedData['companion_plants'])->toHaveCount(3);
        });
    });
});
