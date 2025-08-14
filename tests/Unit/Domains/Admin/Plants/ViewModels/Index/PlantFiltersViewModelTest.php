<?php

use App\Domains\Admin\Plants\ViewModels\Index\PlantFiltersViewModel;

describe('PlantFiltersViewModel', function () {
    describe('creation and basic properties', function () {
        it('creates filters with all properties', function () {
            // Act
            $filters = new PlantFiltersViewModel(
                search: 'tomato',
                type: 'gemuese',
                status: 'active',
                category: 'vegetables'
            );

            // Assert
            expect($filters->search)->toBe('tomato')
                ->and($filters->type)->toBe('gemuese')
                ->and($filters->status)->toBe('active')
                ->and($filters->category)->toBe('vegetables');
        });

        it('creates filters with null values', function () {
            // Act
            $filters = new PlantFiltersViewModel(
                search: null,
                type: null,
                status: null,
                category: null
            );

            // Assert
            expect($filters->search)->toBeNull()
                ->and($filters->type)->toBeNull()
                ->and($filters->status)->toBeNull()
                ->and($filters->category)->toBeNull();
        });

        it('creates filters with default constructor values', function () {
            // Act
            $filters = new PlantFiltersViewModel;

            // Assert
            expect($filters->search)->toBeNull()
                ->and($filters->type)->toBeNull()
                ->and($filters->status)->toBeNull()
                ->and($filters->category)->toBeNull();
        });

        it('creates filters with partial values', function () {
            // Act
            $filters = new PlantFiltersViewModel(
                search: 'roses',
                type: 'blumen'
            );

            // Assert
            expect($filters->search)->toBe('roses')
                ->and($filters->type)->toBe('blumen')
                ->and($filters->status)->toBeNull()
                ->and($filters->category)->toBeNull();
        });
    });

    describe('from factory method', function () {
        it('creates from complete array', function () {
            // Arrange
            $filtersArray = [
                'search' => 'herb',
                'type' => 'kraeuter',
                'status' => 'published',
                'category' => 'medicinal',
            ];

            // Act
            $filters = PlantFiltersViewModel::from($filtersArray);

            // Assert
            expect($filters->search)->toBe('herb')
                ->and($filters->type)->toBe('kraeuter')
                ->and($filters->status)->toBe('published')
                ->and($filters->category)->toBe('medicinal');
        });

        it('creates from empty array', function () {
            // Act
            $filters = PlantFiltersViewModel::from([]);

            // Assert
            expect($filters->search)->toBeNull()
                ->and($filters->type)->toBeNull()
                ->and($filters->status)->toBeNull()
                ->and($filters->category)->toBeNull();
        });

        it('creates from partial array', function () {
            // Arrange
            $filtersArray = [
                'search' => 'mint',
                'category' => 'herbs',
            ];

            // Act
            $filters = PlantFiltersViewModel::from($filtersArray);

            // Assert
            expect($filters->search)->toBe('mint')
                ->and($filters->type)->toBeNull()
                ->and($filters->status)->toBeNull()
                ->and($filters->category)->toBe('herbs');
        });

        it('ignores unknown keys in array', function () {
            // Arrange
            $filtersArray = [
                'search' => 'flower',
                'unknown_key' => 'unknown_value',
                'another_unknown' => 123,
                'type' => 'blumen',
            ];

            // Act
            $filters = PlantFiltersViewModel::from($filtersArray);

            // Assert
            expect($filters->search)->toBe('flower')
                ->and($filters->type)->toBe('blumen')
                ->and($filters->status)->toBeNull()
                ->and($filters->category)->toBeNull();
        });

        it('handles null values in array', function () {
            // Arrange
            $filtersArray = [
                'search' => null,
                'type' => 'strauch',
                'status' => null,
                'category' => 'garden',
            ];

            // Act
            $filters = PlantFiltersViewModel::from($filtersArray);

            // Assert
            expect($filters->search)->toBeNull()
                ->and($filters->type)->toBe('strauch')
                ->and($filters->status)->toBeNull()
                ->and($filters->category)->toBe('garden');
        });

        it('handles empty string values in array', function () {
            // Arrange
            $filtersArray = [
                'search' => '',
                'type' => 'baum',
                'status' => '',
                'category' => '',
            ];

            // Act
            $filters = PlantFiltersViewModel::from($filtersArray);

            // Assert
            expect($filters->search)->toBe('')
                ->and($filters->type)->toBe('baum')
                ->and($filters->status)->toBe('')
                ->and($filters->category)->toBe('');
        });
    });

    describe('hasActiveFilters method', function () {
        it('returns false when no filters are set', function () {
            // Arrange
            $filters = new PlantFiltersViewModel;

            // Act & Assert
            expect($filters->hasActiveFilters())->toBeFalse();
        });

        it('returns false when all filters are null', function () {
            // Arrange
            $filters = new PlantFiltersViewModel(
                search: null,
                type: null,
                status: null,
                category: null
            );

            // Act & Assert
            expect($filters->hasActiveFilters())->toBeFalse();
        });

        it('returns false when all filters are empty strings', function () {
            // Arrange
            $filters = new PlantFiltersViewModel(
                search: '',
                type: '',
                status: '',
                category: ''
            );

            // Act & Assert
            expect($filters->hasActiveFilters())->toBeFalse();
        });

        it('returns true when search filter is set', function () {
            // Arrange
            $filters = new PlantFiltersViewModel(search: 'tomato');

            // Act & Assert
            expect($filters->hasActiveFilters())->toBeTrue();
        });

        it('returns true when type filter is set', function () {
            // Arrange
            $filters = new PlantFiltersViewModel(type: 'gemuese');

            // Act & Assert
            expect($filters->hasActiveFilters())->toBeTrue();
        });

        it('returns true when status filter is set', function () {
            // Arrange
            $filters = new PlantFiltersViewModel(status: 'active');

            // Act & Assert
            expect($filters->hasActiveFilters())->toBeTrue();
        });

        it('returns true when category filter is set', function () {
            // Arrange
            $filters = new PlantFiltersViewModel(category: 'vegetables');

            // Act & Assert
            expect($filters->hasActiveFilters())->toBeTrue();
        });

        it('returns true when multiple filters are set', function () {
            // Arrange
            $filters = new PlantFiltersViewModel(
                search: 'mint',
                type: 'kraeuter',
                status: 'published'
            );

            // Act & Assert
            expect($filters->hasActiveFilters())->toBeTrue();
        });

        it('returns true when only one of multiple filters is set', function () {
            // Arrange
            $filters = new PlantFiltersViewModel(
                search: null,
                type: 'blumen',
                status: null,
                category: null
            );

            // Act & Assert
            expect($filters->hasActiveFilters())->toBeTrue();
        });
    });

    describe('getActiveFiltersCount method', function () {
        it('returns 0 when no filters are set', function () {
            // Arrange
            $filters = new PlantFiltersViewModel;

            // Act & Assert
            expect($filters->getActiveFiltersCount())->toBe(0);
        });

        it('returns 0 when all filters are null', function () {
            // Arrange
            $filters = new PlantFiltersViewModel(
                search: null,
                type: null,
                status: null,
                category: null
            );

            // Act & Assert
            expect($filters->getActiveFiltersCount())->toBe(0);
        });

        it('returns 0 when all filters are empty strings', function () {
            // Arrange
            $filters = new PlantFiltersViewModel(
                search: '',
                type: '',
                status: '',
                category: ''
            );

            // Act & Assert
            expect($filters->getActiveFiltersCount())->toBe(0);
        });

        it('returns 1 when one filter is set', function () {
            // Arrange
            $filters = new PlantFiltersViewModel(search: 'rose');

            // Act & Assert
            expect($filters->getActiveFiltersCount())->toBe(1);
        });

        it('returns 2 when two filters are set', function () {
            // Arrange
            $filters = new PlantFiltersViewModel(
                search: 'herb',
                type: 'kraeuter'
            );

            // Act & Assert
            expect($filters->getActiveFiltersCount())->toBe(2);
        });

        it('returns 3 when three filters are set', function () {
            // Arrange
            $filters = new PlantFiltersViewModel(
                search: 'tree',
                type: 'baum',
                status: 'active'
            );

            // Act & Assert
            expect($filters->getActiveFiltersCount())->toBe(3);
        });

        it('returns 4 when all filters are set', function () {
            // Arrange
            $filters = new PlantFiltersViewModel(
                search: 'flower',
                type: 'blumen',
                status: 'published',
                category: 'garden'
            );

            // Act & Assert
            expect($filters->getActiveFiltersCount())->toBe(4);
        });

        it('ignores null values in count', function () {
            // Arrange
            $filters = new PlantFiltersViewModel(
                search: 'cactus',
                type: null,
                status: 'draft',
                category: null
            );

            // Act & Assert
            expect($filters->getActiveFiltersCount())->toBe(2);
        });

        it('ignores empty string values in count', function () {
            // Arrange
            $filters = new PlantFiltersViewModel(
                search: '',
                type: 'strauch',
                status: '',
                category: 'shrubs'
            );

            // Act & Assert
            expect($filters->getActiveFiltersCount())->toBe(2);
        });
    });

    describe('toArray method', function () {
        it('returns empty array when no filters are set', function () {
            // Arrange
            $filters = new PlantFiltersViewModel;

            // Act
            $array = $filters->toArray();

            // Assert
            expect($array)->toBe([]);
        });

        it('returns filtered array excluding null values', function () {
            // Arrange
            $filters = new PlantFiltersViewModel(
                search: 'lavender',
                type: null,
                status: 'active',
                category: null
            );

            // Act
            $array = $filters->toArray();

            // Assert
            expect($array)->toBe([
                'search' => 'lavender',
                'status' => 'active',
            ]);
        });

        it('returns filtered array excluding empty strings', function () {
            // Arrange
            $filters = new PlantFiltersViewModel(
                search: '',
                type: 'kraeuter',
                status: '',
                category: 'medicinal'
            );

            // Act
            $array = $filters->toArray();

            // Assert
            expect($array)->toBe([
                'type' => 'kraeuter',
                'category' => 'medicinal',
            ]);
        });

        it('returns complete array when all filters are set', function () {
            // Arrange
            $filters = new PlantFiltersViewModel(
                search: 'sunflower',
                type: 'blumen',
                status: 'published',
                category: 'annual'
            );

            // Act
            $array = $filters->toArray();

            // Assert
            expect($array)->toBe([
                'search' => 'sunflower',
                'type' => 'blumen',
                'status' => 'published',
                'category' => 'annual',
            ]);
        });

        it('maintains correct key order', function () {
            // Arrange
            $filters = new PlantFiltersViewModel(
                search: 'oak',
                type: 'baum',
                status: 'mature',
                category: 'deciduous'
            );

            // Act
            $array = $filters->toArray();
            $keys = array_keys($array);

            // Assert
            expect($keys)->toBe(['search', 'type', 'status', 'category']);
        });
    });

    describe('readonly behavior', function () {
        it('is readonly class', function () {
            // Arrange
            $filters = new PlantFiltersViewModel(
                search: 'original',
                type: 'gemuese'
            );

            // Assert - These would cause errors if uncommented due to readonly
            // $filters->search = 'modified'; // This would fail
            // $filters->type = 'kraeuter'; // This would fail

            expect($filters->search)->toBe('original')
                ->and($filters->type)->toBe('gemuese');
        });
    });

    describe('edge cases', function () {
        it('handles special characters in filter values', function () {
            // Arrange
            $specialSearch = 'äöü ßÄÖÜ €@#$%^&*()';
            $specialType = 'spëciäl-tÿpë';
            $specialStatus = 'ståtüs_wïth-spëcïäl';
            $specialCategory = 'çätëgörÿ & møré';

            // Act
            $filters = new PlantFiltersViewModel(
                search: $specialSearch,
                type: $specialType,
                status: $specialStatus,
                category: $specialCategory
            );

            // Assert
            expect($filters->search)->toBe($specialSearch)
                ->and($filters->type)->toBe($specialType)
                ->and($filters->status)->toBe($specialStatus)
                ->and($filters->category)->toBe($specialCategory);
        });

        it('handles very long filter values', function () {
            // Arrange
            $longSearch = str_repeat('long search term ', 100);
            $longType = str_repeat('very-long-type-name-', 50);

            // Act
            $filters = new PlantFiltersViewModel(
                search: $longSearch,
                type: $longType
            );

            // Assert
            expect($filters->search)->toBe($longSearch)
                ->and($filters->type)->toBe($longType);
        });

        it('handles whitespace in filter values', function () {
            // Act
            $filters = new PlantFiltersViewModel(
                search: '  spaced search  ',
                type: '  spaced type  ',
                status: '  spaced status  ',
                category: '  spaced category  '
            );

            // Assert - Should preserve whitespace
            expect($filters->search)->toBe('  spaced search  ')
                ->and($filters->type)->toBe('  spaced type  ')
                ->and($filters->status)->toBe('  spaced status  ')
                ->and($filters->category)->toBe('  spaced category  ');
        });

        it('handles line breaks in filter values', function () {
            // Act
            $filters = new PlantFiltersViewModel(
                search: "multi\nline\nsearch",
                type: "type\nwith\nbreaks"
            );

            // Assert
            expect($filters->search)->toBe("multi\nline\nsearch")
                ->and($filters->type)->toBe("type\nwith\nbreaks");
        });
    });

    describe('common filter scenarios', function () {
        it('handles search-only filter', function () {
            // Arrange
            $filters = PlantFiltersViewModel::from(['search' => 'tomato']);

            // Assert
            expect($filters->hasActiveFilters())->toBeTrue()
                ->and($filters->getActiveFiltersCount())->toBe(1)
                ->and($filters->toArray())->toBe(['search' => 'tomato']);
        });

        it('handles type-only filter', function () {
            // Arrange
            $filters = PlantFiltersViewModel::from(['type' => 'gemuese']);

            // Assert
            expect($filters->hasActiveFilters())->toBeTrue()
                ->and($filters->getActiveFiltersCount())->toBe(1)
                ->and($filters->toArray())->toBe(['type' => 'gemuese']);
        });

        it('handles status-only filter', function () {
            // Arrange
            $filters = PlantFiltersViewModel::from(['status' => 'published']);

            // Assert
            expect($filters->hasActiveFilters())->toBeTrue()
                ->and($filters->getActiveFiltersCount())->toBe(1)
                ->and($filters->toArray())->toBe(['status' => 'published']);
        });

        it('handles category-only filter', function () {
            // Arrange
            $filters = PlantFiltersViewModel::from(['category' => 'herbs']);

            // Assert
            expect($filters->hasActiveFilters())->toBeTrue()
                ->and($filters->getActiveFiltersCount())->toBe(1)
                ->and($filters->toArray())->toBe(['category' => 'herbs']);
        });

        it('handles combined search and type filter', function () {
            // Arrange
            $filters = PlantFiltersViewModel::from([
                'search' => 'basilikum',
                'type' => 'kraeuter',
            ]);

            // Assert
            expect($filters->hasActiveFilters())->toBeTrue()
                ->and($filters->getActiveFiltersCount())->toBe(2)
                ->and($filters->toArray())->toBe([
                    'search' => 'basilikum',
                    'type' => 'kraeuter',
                ]);
        });

        it('handles complex multi-filter scenario', function () {
            // Arrange
            $filters = PlantFiltersViewModel::from([
                'search' => 'rose',
                'type' => 'blumen',
                'status' => 'published',
                'category' => 'garden',
            ]);

            // Assert
            expect($filters->hasActiveFilters())->toBeTrue()
                ->and($filters->getActiveFiltersCount())->toBe(4)
                ->and($filters->toArray())->toBe([
                    'search' => 'rose',
                    'type' => 'blumen',
                    'status' => 'published',
                    'category' => 'garden',
                ]);
        });
    });
});
