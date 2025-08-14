<?php

use App\Domains\Admin\Plants\Mappers\PlantTimelineMapper;
use App\Domains\Admin\Plants\ValueObjects\TimelineEvent;
use Illuminate\Support\Carbon;

describe('PlantTimelineMapper', function () {

    beforeEach(function () {
        $this->mapper = new PlantTimelineMapper;
        $this->testDate = Carbon::parse('2025-01-15 10:30:00');
    });

    describe('mapTimelineEventsFromDatabase', function () {

        it('maps basic event data correctly', function () {
            // Arrange
            $dbEvents = [
                [
                    'event_type' => 'created',
                    'performed_by' => 'Test User',
                    'performed_at' => $this->testDate,
                    'event_details' => [],
                ],
            ];

            // Act
            $result = $this->mapper->mapTimelineEventsFromDatabase($dbEvents, false);

            // Assert
            expect($result)->toHaveCount(1);
            expect($result[0])->toBeInstanceOf(TimelineEvent::class);
            expect($result[0]->type)->toBe('created');
            expect($result[0]->by)->toBe('Test User');
            expect($result[0]->at)->toBe('15.01.2025 10:30');
        });

        it('handles all event types correctly', function () {
            // Arrange
            $eventTypes = [
                'requested',
                'created',
                'updated',
                'update_requested',
                'deleted',
                'restored',
            ];

            $dbEvents = collect($eventTypes)->map(function ($type, $index) {
                return [
                    'event_type' => $type,
                    'performed_by' => "User_{$type}",
                    'performed_at' => $this->testDate->copy()->addMinutes($index * 10),
                    'event_details' => [],
                ];
            })->toArray();

            // Act
            $result = $this->mapper->mapTimelineEventsFromDatabase($dbEvents, false);

            // Assert
            expect($result)->toHaveCount(6);

            foreach ($eventTypes as $index => $expectedType) {
                expect($result[$index]->type)->toBe($expectedType);
                expect($result[$index]->by)->toBe("User_{$expectedType}");
            }
        });

        it('extracts changed fields from updated events', function () {
            // Arrange
            $dbEvents = [
                [
                    'event_type' => 'updated',
                    'performed_by' => 'Editor',
                    'performed_at' => $this->testDate,
                    'event_details' => [
                        'changed_fields' => ['name', 'description'],
                    ],
                ],
            ];

            // Act
            $result = $this->mapper->mapTimelineEventsFromDatabase($dbEvents, false);

            // Assert
            expect($result[0]->details)->toBe(['name', 'description']);
            expect($result[0]->type)->toBe('updated');
            expect($result[0]->hasDetails())->toBeTrue();
        });

        it('extracts requested fields from update_requested events', function () {
            // Arrange
            $dbEvents = [
                [
                    'event_type' => 'update_requested',
                    'performed_by' => 'Contributor',
                    'performed_at' => $this->testDate,
                    'event_details' => [
                        'requested_fields' => ['latin_name', 'category'],
                    ],
                ],
            ];

            // Act
            $result = $this->mapper->mapTimelineEventsFromDatabase($dbEvents, false);

            // Assert
            expect($result[0]->details)->toBe(['latin_name', 'category']);
            expect($result[0]->type)->toBe('update_requested');
            expect($result[0]->hasDetails())->toBeTrue();
        });

        it('handles events with missing event_details', function () {
            // Arrange
            $dbEvents = [
                [
                    'event_type' => 'created',
                    'performed_by' => 'Test User',
                    'performed_at' => $this->testDate,
                    // Missing event_details
                ],
            ];

            // Act
            $result = $this->mapper->mapTimelineEventsFromDatabase($dbEvents, false);

            // Assert
            expect($result)->toHaveCount(1);
            expect($result[0]->type)->toBe('created');
        });

        it('handles empty array gracefully', function () {
            // Act
            $result = $this->mapper->mapTimelineEventsFromDatabase([]);

            // Assert
            expect($result)->toBeEmpty();
        });

        it('throws exception for unknown event type', function () {
            // Arrange
            $dbEvents = [
                [
                    'event_type' => 'unknown_type',
                    'performed_by' => 'Test User',
                    'performed_at' => $this->testDate,
                    'event_details' => [],
                ],
            ];

            // Act & Assert
            expect(fn () => $this->mapper->mapTimelineEventsFromDatabase($dbEvents, false))
                ->toThrow(InvalidArgumentException::class, 'Unknown timeline event type: unknown_type');
        });

        it('handles malformed date formats', function () {
            // Arrange
            $dbEvents = [
                [
                    'event_type' => 'created',
                    'performed_by' => 'Test User',
                    'performed_at' => '2025-01-15T10:30:00Z', // ISO format
                    'event_details' => [],
                ],
            ];

            // Act
            $result = $this->mapper->mapTimelineEventsFromDatabase($dbEvents, false);

            // Assert
            expect($result[0]->at)->toBe('15.01.2025 10:30');
        });

        it('handles string dates correctly', function () {
            // Arrange
            $dbEvents = [
                [
                    'event_type' => 'created',
                    'performed_by' => 'Test User',
                    'performed_at' => '2025-01-15 10:30:00',
                    'event_details' => [],
                ],
            ];

            // Act
            $result = $this->mapper->mapTimelineEventsFromDatabase($dbEvents, false);

            // Assert
            expect($result[0]->at)->toBe('15.01.2025 10:30');
        });

        it('handles Carbon instances correctly', function () {
            // Arrange
            $dbEvents = [
                [
                    'event_type' => 'created',
                    'performed_by' => 'Test User',
                    'performed_at' => $this->testDate,
                    'event_details' => [],
                ],
            ];

            // Act
            $result = $this->mapper->mapTimelineEventsFromDatabase($dbEvents, false);

            // Assert
            expect($result[0]->at)->toBe('15.01.2025 10:30');
        });
    });

    describe('error handling', function () {

        it('handles malformed event structure gracefully', function () {
            // Arrange - Missing required fields
            $dbEvents = [
                [
                    'event_type' => 'created',
                    'performed_by' => 'Test User',
                    // Missing performed_at - should handle gracefully
                    'event_details' => [],
                ],
            ];

            // Act - Should handle gracefully
            $result = $this->mapper->mapTimelineEventsFromDatabase($dbEvents, false);

            // Assert - Should create event with null/default values
            expect($result)->toHaveCount(1);
            expect($result[0]->type)->toBe('created');
            expect($result[0]->by)->toBe('Test User');
        });

        it('handles null values in event_details', function () {
            // Arrange
            $dbEvents = [
                [
                    'event_type' => 'updated',
                    'performed_by' => 'Test User',
                    'performed_at' => $this->testDate,
                    'event_details' => null,
                ],
            ];

            // Act
            $result = $this->mapper->mapTimelineEventsFromDatabase($dbEvents, false);

            // Assert
            expect($result[0]->details)->toBeNull();
            expect($result[0]->hasDetails())->toBeFalse();
        });

        it('handles invalid date formats gracefully', function () {
            // Arrange
            $dbEvents = [
                [
                    'event_type' => 'created',
                    'performed_by' => 'Test User',
                    'performed_at' => 'invalid-date',
                    'event_details' => [],
                ],
            ];

            // Act & Assert
            expect(fn () => $this->mapper->mapTimelineEventsFromDatabase($dbEvents, false))
                ->toThrow(Exception::class);
        });
    });

    describe('performance with large datasets', function () {

        it('handles 100+ events efficiently', function () {
            // Arrange - Create 150 events
            $dbEvents = [];
            for ($i = 0; $i < 150; $i++) {
                $dbEvents[] = [
                    'event_type' => ['created', 'updated', 'deleted'][$i % 3],
                    'performed_by' => "User_{$i}",
                    'performed_at' => $this->testDate->copy()->addMinutes($i),
                    'event_details' => ['changed_fields' => ['field_'.$i]],
                ];
            }

            $startTime = microtime(true);

            // Act
            $result = $this->mapper->mapTimelineEventsFromDatabase($dbEvents, false);

            $endTime = microtime(true);
            $executionTime = $endTime - $startTime;

            // Assert
            expect($result)->toHaveCount(150);
            expect($executionTime)->toBeLessThan(1.0); // Should complete in under 1 second
        });

        it('maintains memory efficiency with large datasets', function () {
            // Arrange
            $initialMemory = memory_get_usage();

            // Create 500 events to test memory usage
            $dbEvents = [];
            for ($i = 0; $i < 500; $i++) {
                $dbEvents[] = [
                    'event_type' => 'created',
                    'performed_by' => "User_{$i}",
                    'performed_at' => $this->testDate->copy()->addMinutes($i),
                    'event_details' => [],
                ];
            }

            // Act
            $result = $this->mapper->mapTimelineEventsFromDatabase($dbEvents, false);

            $finalMemory = memory_get_usage();
            $memoryIncrease = $finalMemory - $initialMemory;

            // Assert
            expect($result)->toHaveCount(500);
            // Memory increase should be reasonable (less than 10MB for 500 events)
            expect($memoryIncrease)->toBeLessThan(10 * 1024 * 1024);
        });
    });

    describe('admin permission context', function () {

        it('respects admin context for sensitive events', function () {
            // Arrange - Use isAdmin parameter instead of mocking auth
            $dbEvents = [
                [
                    'event_type' => 'deleted',
                    'performed_by' => 'Admin User',
                    'performed_at' => $this->testDate,
                    'event_details' => [],
                ],
            ];

            // Act - Pass true for admin
            $result = $this->mapper->mapTimelineEventsFromDatabase($dbEvents, true);

            // Assert
            expect($result[0]->showBy)->toBeTrue();
            expect($result[0]->type)->toBe('deleted');
        });

        it('handles non-admin context for sensitive events', function () {
            // Arrange
            $dbEvents = [
                [
                    'event_type' => 'requested',
                    'performed_by' => 'Regular User',
                    'performed_at' => $this->testDate,
                    'event_details' => [],
                ],
            ];

            // Act - Pass false for non-admin
            $result = $this->mapper->mapTimelineEventsFromDatabase($dbEvents, false);

            // Assert
            expect($result[0]->showBy)->toBeFalse();
            expect($result[0]->type)->toBe('requested');
        });
    });

    describe('createDummyTimelineEvents', function () {

        it('creates basic dummy events', function () {
            // Arrange
            $plantData = [
                'created_at' => $this->testDate,
                'created_by' => 'Test Creator',
                'was_community_requested' => false,
            ];

            // Act
            $result = $this->mapper->createDummyTimelineEvents($plantData, false);

            // Assert
            expect($result)->toHaveCount(3); // created, update_requested, updated
            expect($result[0]->type)->toBe('created');
        });

        it('includes requested event for community plants', function () {
            // Arrange
            $plantData = [
                'created_at' => $this->testDate,
                'created_by' => 'Test Creator',
                'was_community_requested' => true,
            ];

            // Act
            $result = $this->mapper->createDummyTimelineEvents($plantData, false);

            // Assert
            expect($result)->toHaveCount(4); // requested, created, update_requested, updated
            expect($result[0]->type)->toBe('requested');
            expect($result[1]->type)->toBe('created');
        });

        it('handles missing plantData gracefully', function () {
            // Arrange
            $plantData = [
                'created_at' => $this->testDate,
                // Missing other fields
            ];

            // Act
            $result = $this->mapper->createDummyTimelineEvents($plantData, false);

            // Assert
            expect($result)->not->toBeEmpty();
            expect($result[0]->type)->toBe('created');
        });
    });
});
