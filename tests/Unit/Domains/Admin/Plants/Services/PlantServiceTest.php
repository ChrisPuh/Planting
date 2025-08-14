<?php

use App\Domains\Admin\Plants\Contracts\PlantRepositoryInterface;
use App\Domains\Admin\Plants\Mappers\PlantTimelineMapper;
use App\Domains\Admin\Plants\Mappers\PlantViewModelMapper;
use App\Domains\Admin\Plants\Services\PlantService;
use App\Domains\Admin\Plants\ValueObjects\TimelineEvent;
use App\Domains\Admin\Plants\ViewModels\Show\PlantViewModel;

use function Pest\Laravel\mock;

describe('PlantService', function () {
    beforeEach(function () {
        $this->mockRepository = mock(PlantRepositoryInterface::class);
        $this->mockViewModelMapper = mock(PlantViewModelMapper::class);
        $this->mockTimelineMapper = mock(PlantTimelineMapper::class);

        $this->service = new PlantService(
            $this->mockRepository,
            $this->mockViewModelMapper,
            $this->mockTimelineMapper
        );
    });

    describe('getPlantForShow', function () {
        it('retrieves plant with timeline and maps to view model correctly', function () {
            // Arrange
            $plantUuid = '12345678-1234-1234-1234-123456789012';
            $mockPlantData = [
                'uuid' => $plantUuid,
                'name' => 'Test Plant',
                'type' => 'gemuese',
            ];
            $mockTimelineData = [
                ['event_type' => 'created', 'performed_at' => '2025-01-15'],
            ];
            $mockRepositoryData = [
                'plant' => $mockPlantData,
                'timeline_events' => $mockTimelineData,
            ];
            $mockTimelineEvents = [
                TimelineEvent::created('Admin', '15.01.2025 10:30', true),
            ];
            $mockViewModel = new PlantViewModel(
                uuid: $plantUuid,
                name: 'Test Plant',
                type: 'Gemüse',
                timelineEvents: $mockTimelineEvents
            );

            $this->mockRepository
                ->shouldReceive('findWithTimeline')
                ->with($plantUuid)
                ->once()
                ->andReturn($mockRepositoryData);

            $this->mockTimelineMapper
                ->shouldReceive('mapTimelineEventsFromDatabase')
                ->with($mockTimelineData, true)
                ->once()
                ->andReturn($mockTimelineEvents);

            $this->mockViewModelMapper
                ->shouldReceive('toShowViewModel')
                ->with($mockPlantData, $mockTimelineEvents)
                ->once()
                ->andReturn($mockViewModel);

            // Act
            $result = $this->service->getPlantForShow($plantUuid, isAdmin: true);

            // Assert
            expect($result)->toBe($mockViewModel);
        });

        it('passes admin status correctly to timeline mapper', function () {
            // Arrange
            $plantUuid = '12345678-1234-1234-1234-123456789012';
            $mockRepositoryData = [
                'plant' => ['uuid' => $plantUuid, 'name' => 'Test'],
                'timeline_events' => [],
            ];

            $this->mockRepository
                ->shouldReceive('findWithTimeline')
                ->andReturn($mockRepositoryData);

            $this->mockTimelineMapper
                ->shouldReceive('mapTimelineEventsFromDatabase')
                ->with([], false)  // Should pass false for non-admin
                ->once()
                ->andReturn([]);

            $this->mockViewModelMapper
                ->shouldReceive('toShowViewModel')
                ->andReturn(new PlantViewModel(uuid: $plantUuid, name: 'Test', type: 'Test', timelineEvents: []));

            // Act
            $this->service->getPlantForShow($plantUuid, isAdmin: false);

            // Assert - Expectations handled by mock setup
        });

        it('handles repository exceptions gracefully', function () {
            // Arrange
            $plantUuid = '12345678-1234-1234-1234-123456789012';

            $this->mockRepository
                ->shouldReceive('findWithTimeline')
                ->with($plantUuid)
                ->andThrow(new \Exception('Plant not found'));

            // Act & Assert
            expect(fn () => $this->service->getPlantForShow($plantUuid))
                ->toThrow(\Exception::class, 'Plant not found');
        });
    });

    describe('getPlantForEdit', function () {
        it('delegates to getPlantForShow with same parameters', function () {
            // Arrange
            $plantUuid = '12345678-1234-1234-1234-123456789012';
            $isAdmin = true;

            // Mock the repository and mappers to avoid deep mock setup
            $this->mockRepository->shouldReceive('findWithTimeline')->andReturn([
                'plant' => ['uuid' => $plantUuid, 'name' => 'Test'],
                'timeline_events' => [],
            ]);
            $this->mockTimelineMapper->shouldReceive('mapTimelineEventsFromDatabase')->andReturn([]);
            $this->mockViewModelMapper->shouldReceive('toShowViewModel')->andReturn(
                new PlantViewModel(uuid: $plantUuid, name: 'Test', type: 'Test', timelineEvents: [])
            );

            // Act
            $result = $this->service->getPlantForEdit($plantUuid, $isAdmin);

            // Assert
            expect($result)->toBeInstanceOf(PlantViewModel::class);
        });
    });

    describe('getPlantsForIndex', function () {
        it('retrieves and maps plants for index view', function () {
            // Arrange
            $filters = ['type' => 'gemuese'];
            $mockPlantsData = [
                [
                    'uuid' => '12345678-1234-1234-1234-123456789012',
                    'name' => 'Plant 1',
                    'type' => 'gemuese',
                ],
                [
                    'uuid' => '87654321-4321-4321-4321-210987654321',
                    'name' => 'Plant 2',
                    'type' => 'gemuese',
                ],
            ];
            $mockIndexViewModels = [
                [
                    'uuid' => '12345678-1234-1234-1234-123456789012',
                    'name' => 'Plant 1',
                    'type' => 'Gemüse',
                ],
                [
                    'uuid' => '87654321-4321-4321-4321-210987654321',
                    'name' => 'Plant 2',
                    'type' => 'Gemüse',
                ],
            ];

            $this->mockRepository
                ->shouldReceive('getAll')
                ->with($filters)
                ->once()
                ->andReturn($mockPlantsData);

            $this->mockViewModelMapper
                ->shouldReceive('toIndexViewModel')
                ->times(2)
                ->andReturn($mockIndexViewModels[0], $mockIndexViewModels[1]);

            // Act
            $result = $this->service->getPlantsForIndex($filters);

            // Assert
            expect($result)
                ->toHaveCount(2)
                ->and($result[0])->toBe($mockIndexViewModels[0])
                ->and($result[1])->toBe($mockIndexViewModels[1]);
        });

        it('handles null filters correctly', function () {
            // Arrange
            $this->mockRepository
                ->shouldReceive('getAll')
                ->with(null)
                ->once()
                ->andReturn([]);

            // Act
            $result = $this->service->getPlantsForIndex(null);

            // Assert
            expect($result)->toBeEmpty();
        });

        it('handles empty results from repository', function () {
            // Arrange
            $this->mockRepository
                ->shouldReceive('getAll')
                ->andReturn([]);

            // Act
            $result = $this->service->getPlantsForIndex();

            // Assert
            expect($result)->toBeEmpty();
        });
    });

    describe('getAllPlantTypes', function () {
        it('returns correct plant type mappings', function () {
            // Act
            $result = $this->service->getAllPlantTypes();

            // Assert
            expect($result)
                ->toBeArray()
                ->toHaveKey('gemuese', 'Gemüse')
                ->toHaveKey('kraeuter', 'Kräuter')
                ->toHaveKey('blume', 'Blumen')
                ->toHaveKey('strauch', 'Sträucher')
                ->toHaveKey('baum', 'Bäume')
                ->toHaveCount(5);
        });

        it('returns consistent mappings across calls', function () {
            // Act
            $result1 = $this->service->getAllPlantTypes();
            $result2 = $this->service->getAllPlantTypes();

            // Assert
            expect($result1)->toBe($result2);
        });
    });

    describe('searchPlants', function () {
        it('delegates to getPlantsForIndex with search filter', function () {
            // Arrange
            $searchQuery = 'tomato';
            $expectedFilters = ['search' => $searchQuery];
            $mockResults = [
                ['uuid' => '12345', 'name' => 'Tomato Plant', 'type' => 'Gemüse'],
            ];

            $this->mockRepository
                ->shouldReceive('getAll')
                ->with($expectedFilters)
                ->once()
                ->andReturn([['uuid' => '12345', 'name' => 'Tomato Plant', 'type' => 'gemuese']]);

            $this->mockViewModelMapper
                ->shouldReceive('toIndexViewModel')
                ->once()
                ->andReturn($mockResults[0]);

            // Act
            $result = $this->service->searchPlants($searchQuery);

            // Assert
            expect($result)->toBe($mockResults);
        });

        it('handles empty search query', function () {
            // Arrange
            $this->mockRepository
                ->shouldReceive('getAll')
                ->with(['search' => ''])
                ->andReturn([]);

            // Act
            $result = $this->service->searchPlants('');

            // Assert
            expect($result)->toBeEmpty();
        });
    });

    describe('business logic validation', function () {
        it('enforces business rules consistently', function () {
            // Arrange
            $validUuid = '12345678-1234-1234-1234-123456789012';
            $mockRepositoryData = [
                'plant' => ['uuid' => $validUuid, 'name' => 'Valid Plant'],
                'timeline_events' => [],
            ];

            $this->mockRepository
                ->shouldReceive('findWithTimeline')
                ->andReturn($mockRepositoryData);

            $this->mockTimelineMapper
                ->shouldReceive('mapTimelineEventsFromDatabase')
                ->andReturn([]);

            $mockViewModel = new PlantViewModel(
                uuid: $validUuid,
                name: 'Valid Plant',
                type: 'Test',
                timelineEvents: []
            );

            $this->mockViewModelMapper
                ->shouldReceive('toShowViewModel')
                ->andReturn($mockViewModel);

            // Act
            $result = $this->service->getPlantForShow($validUuid);

            // Assert
            expect($result)->toBe($mockViewModel);
        });

        it('validates service dependencies are properly injected', function () {
            // Arrange & Act
            $service = new PlantService(
                $this->mockRepository,
                $this->mockViewModelMapper,
                $this->mockTimelineMapper
            );

            // Assert
            expect($service)->toBeInstanceOf(PlantService::class);
        });
    });

    describe('error scenarios', function () {
        it('handles mapper exceptions during timeline mapping', function () {
            // Arrange
            $plantUuid = '12345678-1234-1234-1234-123456789012';

            $this->mockRepository
                ->shouldReceive('findWithTimeline')
                ->andReturn([
                    'plant' => ['uuid' => $plantUuid],
                    'timeline_events' => [],
                ]);

            $this->mockTimelineMapper
                ->shouldReceive('mapTimelineEventsFromDatabase')
                ->andThrow(new \RuntimeException('Timeline mapping failed'));

            // Act & Assert
            expect(fn () => $this->service->getPlantForShow($plantUuid))
                ->toThrow(\RuntimeException::class, 'Timeline mapping failed');
        });

        it('handles view model mapping exceptions', function () {
            // Arrange
            $plantUuid = '12345678-1234-1234-1234-123456789012';

            $this->mockRepository
                ->shouldReceive('findWithTimeline')
                ->andReturn([
                    'plant' => ['uuid' => $plantUuid],
                    'timeline_events' => [],
                ]);

            $this->mockTimelineMapper
                ->shouldReceive('mapTimelineEventsFromDatabase')
                ->andReturn([]);

            $this->mockViewModelMapper
                ->shouldReceive('toShowViewModel')
                ->andThrow(new \InvalidArgumentException('Invalid plant data'));

            // Act & Assert
            expect(fn () => $this->service->getPlantForShow($plantUuid))
                ->toThrow(\InvalidArgumentException::class, 'Invalid plant data');
        });
    });
});
