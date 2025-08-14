<?php

use App\Domains\Admin\Plants\Mappers\PlantViewModelMapper;
use App\Domains\Admin\Plants\ValueObjects\TimelineEvent;
use App\Domains\Admin\Plants\ViewModels\Show\PlantViewModel;
use Illuminate\Support\Carbon;

describe('PlantViewModelMapper', function () {

    beforeEach(function () {
        $this->mapper = new PlantViewModelMapper;
        $this->testDate = Carbon::parse('2025-01-15 10:30:00');
    });

    describe('toShowViewModel', function () {

        it('maps basic plant data correctly', function () {
            // Arrange
            $plantData = [
                'uuid' => '12345678-1234-1234-1234-123456789012',
                'name' => 'Test Tomato',
                'type' => 'gemuese',
                'image_url' => 'https://example.com/tomato.jpg',
                'category' => 'Nightshades',
                'latin_name' => 'Solanum lycopersicum',
                'description' => 'A delicious red tomato',
                'created_at' => $this->testDate,
                'created_by' => 'Test User',
            ];

            $timelineEvents = [];

            // Act
            $result = $this->mapper->toShowViewModel($plantData, $timelineEvents);

            // Assert
            expect($result)->toBeInstanceOf(PlantViewModel::class);
            expect($result->uuid)->toBe('12345678-1234-1234-1234-123456789012');
            expect($result->name)->toBe('Test Tomato');
            expect($result->type)->toBe('Gemüse'); // Mapped from 'gemuese'
            expect($result->image_url)->toBe('https://example.com/tomato.jpg');
        });

        it('maps all plant types to display names correctly', function () {
            // Arrange
            $typeMapping = [
                'gemuese' => 'Gemüse',
                'kraeuter' => 'Kräuter',
                'blume' => 'Blumen',
                'strauch' => 'Sträucher',
                'baum' => 'Bäume',
                'unknown' => 'Unknown', // Default case
            ];

            foreach ($typeMapping as $type => $expectedDisplay) {
                $plantData = [
                    'uuid' => '12345678-1234-1234-1234-123456789012',
                    'name' => 'Test Plant',
                    'type' => $type,
                    'image_url' => null,
                    'category' => null,
                    'latin_name' => null,
                    'description' => null,
                ];

                // Act
                $result = $this->mapper->toShowViewModel($plantData, []);

                // Assert
                expect($result->type)->toBe($expectedDisplay);
            }
        });

        it('extracts metadata from timeline events correctly', function () {
            // Arrange
            $plantData = [
                'uuid' => '12345678-1234-1234-1234-123456789012',
                'name' => 'Timeline Plant',
                'type' => 'gemuese',
                'image_url' => null,
                'category' => null,
                'latin_name' => null,
                'description' => null,
            ];

            $timelineEvents = [
                TimelineEvent::requested('Community User', '10.01.2025 09:00', false),
                TimelineEvent::created('Admin User', '15.01.2025 10:30', true),
                TimelineEvent::updated('Editor User', '20.01.2025 14:15', true, ['description']),
            ];

            // Act
            $result = $this->mapper->toShowViewModel($plantData, $timelineEvents);
            $metadata = $result->getMetadata();

            // Assert
            expect($metadata->requestedBy)->toBe('Community User');
            expect($metadata->requestedAt)->toBe('10.01.2025 09:00');
            expect($metadata->createdBy)->toBe('Admin User');
            expect($metadata->createdAt)->toBe('15.01.2025 10:30');
            expect($metadata->updatedBy)->toBe('Editor User');
            expect($metadata->updatedAt)->toBe('20.01.2025 14:15');
        });

        it('handles deleted and restored plants correctly', function () {
            // Arrange
            $plantData = [
                'uuid' => '12345678-1234-1234-1234-123456789012',
                'name' => 'Deleted Plant',
                'type' => 'gemuese',
                'image_url' => null,
                'category' => null,
                'latin_name' => null,
                'description' => null,
            ];

            $timelineEvents = [
                TimelineEvent::created('Admin User', '15.01.2025 10:30', true),
                TimelineEvent::deleted('Admin User', '20.01.2025 14:15', true),
                TimelineEvent::restored('Super Admin', '25.01.2025 09:00', true),
            ];

            // Act
            $result = $this->mapper->toShowViewModel($plantData, $timelineEvents);
            $metadata = $result->getMetadata();

            // Assert - After restoration, delete info should be cleared
            expect($metadata->deletedBy)->toBeNull();
            expect($metadata->deletedAt)->toBeNull();
        });

        it('falls back to plant data when timeline is empty', function () {
            // Arrange
            $plantData = [
                'uuid' => '12345678-1234-1234-1234-123456789012',
                'name' => 'Fallback Plant',
                'type' => 'gemuese',
                'image_url' => null,
                'category' => null,
                'latin_name' => null,
                'description' => null,
                'created_at' => $this->testDate,
                'created_by' => 'Database User',
            ];

            $timelineEvents = []; // Empty timeline

            // Act
            $result = $this->mapper->toShowViewModel($plantData, $timelineEvents);
            $metadata = $result->getMetadata();

            // Assert
            expect($metadata->createdBy)->toBe('Database User');
            expect($metadata->createdAt)->toBe($this->testDate->toDateTimeString());
        });

        it('handles null and missing values gracefully', function () {
            // Arrange
            $plantData = [
                'uuid' => '12345678-1234-1234-1234-123456789012',
                'name' => 'Minimal Plant',
                'type' => 'gemuese',
                'image_url' => null,
                'category' => null,
                'latin_name' => null,
                'description' => null,
            ];

            $timelineEvents = [];

            // Act
            $result = $this->mapper->toShowViewModel($plantData, $timelineEvents);

            // Assert
            expect($result->uuid)->toBe('12345678-1234-1234-1234-123456789012');
            expect($result->name)->toBe('Minimal Plant');
            expect($result->image_url)->toBeNull();
            expect($result->getMetadata()->createdBy)->toBeNull();
        });

        it('passes timeline events to the view model', function () {
            // Arrange
            $plantData = [
                'uuid' => '12345678-1234-1234-1234-123456789012',
                'name' => 'Timeline Plant',
                'type' => 'gemuese',
                'image_url' => null,
                'category' => null,
                'latin_name' => null,
                'description' => null,
            ];

            $timelineEvents = [
                TimelineEvent::created('Admin User', '15.01.2025 10:30', true),
                TimelineEvent::updated('Editor User', '20.01.2025 14:15', true, ['description']),
            ];

            // Act
            $result = $this->mapper->toShowViewModel($plantData, $timelineEvents);

            // Assert
            $metadataTimeline = $result->getMetadata()->getTimelineEvents();
            expect($metadataTimeline)->toHaveCount(2);
            expect($metadataTimeline->first()->type)->toBe('created');
            expect($metadataTimeline->last()->type)->toBe('updated');
        });
    });

    describe('toIndexViewModel', function () {

        it('maps index view data correctly', function () {
            // Arrange
            $plantData = [
                'uuid' => '12345678-1234-1234-1234-123456789012',
                'name' => 'Index Plant',
                'type' => 'kraeuter',
                'category' => 'Herbs',
                'image_url' => 'https://example.com/herb.jpg',
                'is_deleted' => false,
                'was_community_requested' => true,
                'last_event_at' => $this->testDate,
            ];

            // Act
            $result = $this->mapper->toIndexViewModel($plantData);

            // Assert
            expect($result)->toBe([
                'uuid' => '12345678-1234-1234-1234-123456789012',
                'name' => 'Index Plant',
                'type' => 'Kräuter', // Mapped from 'kraeuter'
                'category' => 'Herbs',
                'image_url' => 'https://example.com/herb.jpg',
                'is_deleted' => false,
                'was_community_requested' => true,
                'last_event_at' => $this->testDate,
            ]);
        });

        it('handles all plant types in index view', function () {
            // Arrange
            $types = ['gemuese', 'kraeuter', 'blume', 'strauch', 'baum'];
            $expectedTypes = ['Gemüse', 'Kräuter', 'Blumen', 'Sträucher', 'Bäume'];

            foreach ($types as $index => $type) {
                $plantData = [
                    'uuid' => "1234567{$index}-1234-1234-1234-123456789012",
                    'name' => "Test {$type}",
                    'type' => $type,
                    'category' => null,
                    'image_url' => null,
                    'is_deleted' => false,
                    'was_community_requested' => false,
                    'last_event_at' => null,
                ];

                // Act
                $result = $this->mapper->toIndexViewModel($plantData);

                // Assert
                expect($result['type'])->toBe($expectedTypes[$index]);
            }
        });

        it('handles deleted plants in index view', function () {
            // Arrange
            $plantData = [
                'uuid' => '12345678-1234-1234-1234-123456789012',
                'name' => 'Deleted Plant',
                'type' => 'gemuese',
                'category' => null,
                'image_url' => null,
                'is_deleted' => true,
                'was_community_requested' => false,
                'last_event_at' => $this->testDate,
            ];

            // Act
            $result = $this->mapper->toIndexViewModel($plantData);

            // Assert
            expect($result['is_deleted'])->toBeTrue();
            expect($result['name'])->toBe('Deleted Plant');
        });

        it('handles community requested plants in index view', function () {
            // Arrange
            $plantData = [
                'uuid' => '12345678-1234-1234-1234-123456789012',
                'name' => 'Community Plant',
                'type' => 'blume',
                'category' => 'Flowers',
                'image_url' => null,
                'is_deleted' => false,
                'was_community_requested' => true,
                'last_event_at' => $this->testDate,
            ];

            // Act
            $result = $this->mapper->toIndexViewModel($plantData);

            // Assert
            expect($result['was_community_requested'])->toBeTrue();
            expect($result['type'])->toBe('Blumen');
        });
    });

    describe('extractMetadataFromTimeline', function () {

        it('extracts metadata in correct chronological order', function () {
            // Arrange
            $plantData = [
                'uuid' => '12345678-1234-1234-1234-123456789012',
                'name' => 'Order Test Plant',
                'type' => 'gemuese',
                'image_url' => null,
                'category' => null,
                'latin_name' => null,
                'description' => null,
            ];

            // Events in chronological order (as they would be processed)
            $timelineEvents = [
                TimelineEvent::requested('Community User', '10.01.2025 09:00', false),      // 1. (erstes Ereignis)
                TimelineEvent::created('Admin User', '15.01.2025 10:30', true),             // 2.
                TimelineEvent::updated('Editor 1', '20.01.2025 14:15', true, ['description']), // 3.
                TimelineEvent::updated('Editor 2', '25.01.2025 16:00', true, ['category']), // 4. (letztes Update)
            ];

            // Act
            $result = $this->mapper->toShowViewModel($plantData, $timelineEvents);
            $metadata = $result->getMetadata();

            // Assert - Should use the LAST occurrence of each event type after chronological sorting
            expect($metadata->requestedBy)->toBe('Community User');
            expect($metadata->createdBy)->toBe('Admin User');
            expect($metadata->updatedBy)->toBe('Editor 2'); // Last update chronologically
            expect($metadata->updatedAt)->toBe('25.01.2025 16:00');
        });

        it('handles multiple updates correctly', function () {
            // Arrange
            $plantData = [
                'uuid' => '12345678-1234-1234-1234-123456789012',
                'name' => 'Multi Update Plant',
                'type' => 'gemuese',
                'image_url' => null,
                'category' => null,
                'latin_name' => null,
                'description' => null,
            ];

            $timelineEvents = [
                TimelineEvent::created('Admin User', '15.01.2025 10:30', true),
                TimelineEvent::updated('Editor 1', '16.01.2025 11:00', true, ['description']),
                TimelineEvent::updated('Editor 2', '17.01.2025 12:00', true, ['category']),
                TimelineEvent::updated('Editor 3', '18.01.2025 13:00', true, ['latin_name']),
            ];

            // Act
            $result = $this->mapper->toShowViewModel($plantData, $timelineEvents);
            $metadata = $result->getMetadata();

            // Assert - Should use the most recent update
            expect($metadata->updatedBy)->toBe('Editor 3');
            expect($metadata->updatedAt)->toBe('18.01.2025 13:00');
        });

        it('handles delete and restore workflow', function () {
            // Arrange
            $plantData = [
                'uuid' => '12345678-1234-1234-1234-123456789012',
                'name' => 'Delete Restore Plant',
                'type' => 'gemuese',
                'image_url' => null,
                'category' => null,
                'latin_name' => null,
                'description' => null,
            ];

            $timelineEvents = [
                TimelineEvent::created('Admin User', '15.01.2025 10:30', true),
                TimelineEvent::deleted('Admin User', '20.01.2025 14:15', true),
                // No restore event - should keep delete info
            ];

            // Act
            $result = $this->mapper->toShowViewModel($plantData, $timelineEvents);
            $metadata = $result->getMetadata();

            // Assert - Delete info should be present
            expect($metadata->deletedBy)->toBe('Admin User');
            expect($metadata->deletedAt)->toBe('20.01.2025 14:15');
        });
    });

    describe('edge cases and error handling', function () {

        it('handles missing required fields gracefully', function () {
            // Arrange
            $plantData = [
                'uuid' => '12345678-1234-1234-1234-123456789012',
                'name' => 'Minimal Plant',
                'type' => 'gemuese',
                // Missing optional fields
            ];

            $timelineEvents = [];

            // Act
            $result = $this->mapper->toShowViewModel($plantData, $timelineEvents);

            // Assert
            expect($result->uuid)->toBe('12345678-1234-1234-1234-123456789012');
            expect($result->name)->toBe('Minimal Plant');
            expect($result->type)->toBe('Gemüse');
        });

        it('handles malformed timeline events gracefully', function () {
            // Arrange
            $plantData = [
                'uuid' => '12345678-1234-1234-1234-123456789012',
                'name' => 'Test Plant',
                'type' => 'gemuese',
                'image_url' => null,
                'category' => null,
                'latin_name' => null,
                'description' => null,
            ];

            // Mix of valid and potentially problematic timeline events
            $timelineEvents = [
                TimelineEvent::created('Admin User', '15.01.2025 10:30', true),
                // Add more events if TimelineEvent supports them
            ];

            // Act
            $result = $this->mapper->toShowViewModel($plantData, $timelineEvents);

            // Assert
            expect($result)->toBeInstanceOf(PlantViewModel::class);
        });

        it('handles complex data structure mapping', function () {
            // Arrange
            $plantData = [
                'uuid' => '12345678-1234-1234-1234-123456789012',
                'name' => 'Complex Plant',
                'type' => 'gemuese',
                'image_url' => 'https://example.com/complex.jpg',
                'category' => 'Complex Category',
                'latin_name' => 'Complexus plantus',
                'description' => 'A very complex plant with lots of details and information.',
                'created_at' => $this->testDate,
                'created_by' => 'Complex User',
                'additional_field' => 'Should be ignored', // Extra field
            ];

            // Events in chronological order
            $timelineEvents = [
                TimelineEvent::requested('Community User', '10.01.2025 09:00', false),
                TimelineEvent::created('Admin User', '15.01.2025 10:30', true),
                TimelineEvent::updated('Editor 1', '16.01.2025 11:00', true, ['description']),
                TimelineEvent::updated('Editor 2', '17.01.2025 12:00', true, ['category', 'latin_name']), // Last update
                TimelineEvent::deleted('Admin User', '20.01.2025 14:15', true),
                TimelineEvent::restored('Super Admin', '25.01.2025 09:00', true),
            ];

            // Act
            $result = $this->mapper->toShowViewModel($plantData, $timelineEvents);

            // Assert
            expect($result->uuid)->toBe('12345678-1234-1234-1234-123456789012');
            expect($result->name)->toBe('Complex Plant');
            expect($result->type)->toBe('Gemüse');

            $metadata = $result->getMetadata();
            expect($metadata->requestedBy)->toBe('Community User');
            expect($metadata->createdBy)->toBe('Admin User');
            expect($metadata->updatedBy)->toBe('Editor 2'); // Last update before delete
            expect($metadata->deletedBy)->toBeNull(); // Cleared by restore
            expect($metadata->deletedAt)->toBeNull(); // Cleared by restore
        });

        it('throws exception for missing UUID', function () {
            // Arrange
            $plantData = [
                'uuid' => null, // This should cause an exception
                'name' => 'Test Plant',
                'type' => 'gemuese',
            ];

            $timelineEvents = [];

            // Act & Assert
            expect(fn () => $this->mapper->toShowViewModel($plantData, $timelineEvents))
                ->toThrow(\InvalidArgumentException::class, 'Plant UUID is required for show view');
        });

        it('handles null values in complex mapping', function () {
            // Arrange - Test with missing optional fields but valid required fields
            $plantData = [
                'uuid' => '12345678-1234-1234-1234-123456789012', // UUID is required
                'name' => 'Minimal Plant',
                'type' => 'gemuese',
                'image_url' => null,
                'category' => null,
                'latin_name' => null,
                'description' => null,
            ];

            $timelineEvents = [];

            // Act
            $result = $this->mapper->toShowViewModel($plantData, $timelineEvents);

            // Assert - Should handle gracefully
            expect($result)->toBeInstanceOf(PlantViewModel::class);
            expect($result->uuid)->toBe('12345678-1234-1234-1234-123456789012');
            expect($result->name)->toBe('Minimal Plant');
            expect($result->image_url)->toBeNull();
            expect($result->getMetadata()->createdBy)->toBeNull();
            expect($result->getDetails()->getCategory()->isMissing)->toBeTrue();
        });
    });
});
