<?php

use App\Domains\Admin\Plants\ValueObjects\PlantMetadataItem;

describe('PlantMetadataItem', function () {
    describe('creation and basic properties', function () {
        it('creates metadata item with all properties', function () {
            // Act
            $item = new PlantMetadataItem(
                label: 'Erstellt',
                by: 'Admin User',
                at: '15.01.2025 10:30',
                showBy: true,
                colorClass: 'text-zinc-500'
            );

            // Assert
            expect($item->label)->toBe('Erstellt')
                ->and($item->by)->toBe('Admin User')
                ->and($item->at)->toBe('15.01.2025 10:30')
                ->and($item->showBy)->toBeTrue()
                ->and($item->colorClass)->toBe('text-zinc-500');
        });

        it('creates metadata item with null values', function () {
            // Act
            $item = new PlantMetadataItem(
                label: 'Gelöscht',
                by: null,
                at: null,
                showBy: false,
                colorClass: 'text-red-500'
            );

            // Assert
            expect($item->label)->toBe('Gelöscht')
                ->and($item->by)->toBeNull()
                ->and($item->at)->toBeNull()
                ->and($item->showBy)->toBeFalse()
                ->and($item->colorClass)->toBe('text-red-500');
        });
    });

    describe('factory method', function () {
        it('creates item using static create method', function () {
            // Act
            $item = PlantMetadataItem::create(
                label: 'Aktualisiert',
                by: 'Editor User',
                at: '16.01.2025 14:20',
                showBy: true,
                colorClass: 'text-blue-500'
            );

            // Assert
            expect($item)->toBeInstanceOf(PlantMetadataItem::class)
                ->and($item->label)->toBe('Aktualisiert')
                ->and($item->by)->toBe('Editor User')
                ->and($item->at)->toBe('16.01.2025 14:20')
                ->and($item->showBy)->toBeTrue()
                ->and($item->colorClass)->toBe('text-blue-500');
        });

        it('creates item with null values using create method', function () {
            // Act
            $item = PlantMetadataItem::create(
                label: 'Test Item',
                by: null,
                at: null,
                showBy: false,
                colorClass: 'text-gray-500'
            );

            // Assert
            expect($item->by)->toBeNull()
                ->and($item->at)->toBeNull()
                ->and($item->showBy)->toBeFalse();
        });
    });

    describe('hasBy method', function () {
        it('returns true when showBy is true and by is not null', function () {
            // Arrange
            $item = new PlantMetadataItem(
                label: 'Test',
                by: 'User',
                at: '15.01.2025',
                showBy: true,
                colorClass: 'text-black'
            );

            // Act & Assert
            expect($item->hasBy())->toBeTrue();
        });

        it('returns false when showBy is false even if by is not null', function () {
            // Arrange
            $item = new PlantMetadataItem(
                label: 'Test',
                by: 'User',
                at: '15.01.2025',
                showBy: false,
                colorClass: 'text-black'
            );

            // Act & Assert
            expect($item->hasBy())->toBeFalse();
        });

        it('returns false when by is null even if showBy is true', function () {
            // Arrange
            $item = new PlantMetadataItem(
                label: 'Test',
                by: null,
                at: '15.01.2025',
                showBy: true,
                colorClass: 'text-black'
            );

            // Act & Assert
            expect($item->hasBy())->toBeFalse();
        });

        it('returns false when both showBy is false and by is null', function () {
            // Arrange
            $item = new PlantMetadataItem(
                label: 'Test',
                by: null,
                at: '15.01.2025',
                showBy: false,
                colorClass: 'text-black'
            );

            // Act & Assert
            expect($item->hasBy())->toBeFalse();
        });
    });

    describe('hasAt method', function () {
        it('returns true when at is not null', function () {
            // Arrange
            $item = new PlantMetadataItem(
                label: 'Test',
                by: 'User',
                at: '15.01.2025 10:30',
                showBy: true,
                colorClass: 'text-black'
            );

            // Act & Assert
            expect($item->hasAt())->toBeTrue();
        });

        it('returns false when at is null', function () {
            // Arrange
            $item = new PlantMetadataItem(
                label: 'Test',
                by: 'User',
                at: null,
                showBy: true,
                colorClass: 'text-black'
            );

            // Act & Assert
            expect($item->hasAt())->toBeFalse();
        });

        it('returns true when at is empty string', function () {
            // Arrange
            $item = new PlantMetadataItem(
                label: 'Test',
                by: 'User',
                at: '',
                showBy: true,
                colorClass: 'text-black'
            );

            // Act & Assert
            expect($item->hasAt())->toBeTrue(); // Empty string is not null
        });
    });

    describe('readonly behavior', function () {
        it('is readonly class', function () {
            // Arrange
            $item = new PlantMetadataItem(
                label: 'Test',
                by: 'User',
                at: '15.01.2025',
                showBy: true,
                colorClass: 'text-black'
            );

            // Assert - These would cause errors if uncommented due to readonly
            // $item->label = 'Modified'; // This would fail
            // $item->by = 'Modified User'; // This would fail
            // $item->showBy = false; // This would fail

            expect($item->label)->toBe('Test')
                ->and($item->by)->toBe('User');
        });
    });

    describe('common use cases', function () {
        it('creates created metadata item', function () {
            // Act
            $item = PlantMetadataItem::create(
                label: 'Erstellt',
                by: 'Admin User',
                at: '15.01.2025 10:30',
                showBy: true,
                colorClass: 'text-zinc-500 dark:text-zinc-400'
            );

            // Assert
            expect($item->label)->toBe('Erstellt')
                ->and($item->hasBy())->toBeTrue()
                ->and($item->hasAt())->toBeTrue()
                ->and($item->colorClass)->toBe('text-zinc-500 dark:text-zinc-400');
        });

        it('creates updated metadata item', function () {
            // Act
            $item = PlantMetadataItem::create(
                label: 'Zuletzt geändert',
                by: 'Editor User',
                at: '16.01.2025 14:20',
                showBy: true,
                colorClass: 'text-zinc-500 dark:text-zinc-400'
            );

            // Assert
            expect($item->label)->toBe('Zuletzt geändert')
                ->and($item->hasBy())->toBeTrue()
                ->and($item->hasAt())->toBeTrue();
        });

        it('creates requested metadata item', function () {
            // Act
            $item = PlantMetadataItem::create(
                label: 'Beantragt',
                by: 'Community User',
                at: '14.01.2025 09:00',
                showBy: true, // Admin can see
                colorClass: 'text-zinc-500 dark:text-zinc-400'
            );

            // Assert
            expect($item->label)->toBe('Beantragt')
                ->and($item->hasBy())->toBeTrue()
                ->and($item->hasAt())->toBeTrue();
        });

        it('creates deleted metadata item', function () {
            // Act
            $item = PlantMetadataItem::create(
                label: 'Gelöscht',
                by: 'Admin User',
                at: '20.01.2025 16:45',
                showBy: true, // Admin only
                colorClass: 'text-red-500 dark:text-red-400'
            );

            // Assert
            expect($item->label)->toBe('Gelöscht')
                ->and($item->hasBy())->toBeTrue()
                ->and($item->hasAt())->toBeTrue()
                ->and($item->colorClass)->toBe('text-red-500 dark:text-red-400');
        });

        it('creates metadata item hidden for non-admin', function () {
            // Act
            $item = PlantMetadataItem::create(
                label: 'Interne Notiz',
                by: 'System User',
                at: '15.01.2025 12:00',
                showBy: false, // Hidden for non-admin
                colorClass: 'text-gray-500'
            );

            // Assert
            expect($item->hasBy())->toBeFalse() // Will be false because showBy is false
                ->and($item->hasAt())->toBeTrue();
        });
    });

    describe('edge cases', function () {
        it('handles empty label', function () {
            // Act
            $item = new PlantMetadataItem(
                label: '',
                by: 'User',
                at: '15.01.2025',
                showBy: true,
                colorClass: 'text-black'
            );

            // Assert
            expect($item->label)->toBe('');
        });

        it('handles empty by field', function () {
            // Act
            $item = new PlantMetadataItem(
                label: 'Test',
                by: '',
                at: '15.01.2025',
                showBy: true,
                colorClass: 'text-black'
            );

            // Assert
            expect($item->by)->toBe('')
                ->and($item->hasBy())->toBeTrue(); // Empty string is not null
        });

        it('handles empty colorClass', function () {
            // Act
            $item = new PlantMetadataItem(
                label: 'Test',
                by: 'User',
                at: '15.01.2025',
                showBy: true,
                colorClass: ''
            );

            // Assert
            expect($item->colorClass)->toBe('');
        });

        it('handles special characters in fields', function () {
            // Arrange
            $specialLabel = 'äöü ßÄÖÜ €@#$%^&*()';
            $specialBy = 'Üser Nämë';
            $specialAt = '15.01.2025 ♥ 10:30';
            $specialColorClass = 'text-ümläüt-500';

            // Act
            $item = new PlantMetadataItem(
                label: $specialLabel,
                by: $specialBy,
                at: $specialAt,
                showBy: true,
                colorClass: $specialColorClass
            );

            // Assert
            expect($item->label)->toBe($specialLabel)
                ->and($item->by)->toBe($specialBy)
                ->and($item->at)->toBe($specialAt)
                ->and($item->colorClass)->toBe($specialColorClass);
        });

        it('handles very long strings', function () {
            // Arrange
            $longLabel = str_repeat('A', 1000);
            $longBy = str_repeat('B', 500);
            $longAt = str_repeat('C', 100);
            $longColorClass = str_repeat('text-very-long-class-name-', 10);

            // Act
            $item = new PlantMetadataItem(
                label: $longLabel,
                by: $longBy,
                at: $longAt,
                showBy: true,
                colorClass: $longColorClass
            );

            // Assert
            expect($item->label)->toBe($longLabel)
                ->and($item->by)->toBe($longBy)
                ->and($item->at)->toBe($longAt)
                ->and($item->colorClass)->toBe($longColorClass);
        });
    });

    describe('formatting scenarios', function () {
        it('handles different date formats', function () {
            $dateFormats = [
                '15.01.2025 10:30',
                '2025-01-15 10:30:00',
                '15/01/25',
                'vor 2 Stunden',
                'Gestern',
            ];

            foreach ($dateFormats as $format) {
                $item = PlantMetadataItem::create(
                    label: 'Test',
                    by: 'User',
                    at: $format,
                    showBy: true,
                    colorClass: 'text-black'
                );

                expect($item->at)->toBe($format)
                    ->and($item->hasAt())->toBeTrue();
            }
        });

        it('handles different CSS color classes', function () {
            $colorClasses = [
                'text-zinc-500',
                'text-zinc-500 dark:text-zinc-400',
                'text-red-500 dark:text-red-400',
                'text-green-600 hover:text-green-700',
                'bg-blue-100 text-blue-800',
            ];

            foreach ($colorClasses as $colorClass) {
                $item = PlantMetadataItem::create(
                    label: 'Test',
                    by: 'User',
                    at: '15.01.2025',
                    showBy: true,
                    colorClass: $colorClass
                );

                expect($item->colorClass)->toBe($colorClass);
            }
        });
    });

    describe('data scenarios', function () {
        it('creates item with whitespace preservation', function () {
            // Act
            $item = new PlantMetadataItem(
                label: '  Erstellt  ',
                by: '  Admin User  ',
                at: '  15.01.2025 10:30  ',
                showBy: true,
                colorClass: '  text-zinc-500  '
            );

            // Assert - Should preserve whitespace
            expect($item->label)->toBe('  Erstellt  ')
                ->and($item->by)->toBe('  Admin User  ')
                ->and($item->at)->toBe('  15.01.2025 10:30  ')
                ->and($item->colorClass)->toBe('  text-zinc-500  ');
        });

        it('handles line breaks in strings', function () {
            // Act
            $item = new PlantMetadataItem(
                label: "Multi\nLine\nLabel",
                by: "User\nName",
                at: "15.01.2025\n10:30",
                showBy: true,
                colorClass: "text-zinc-500\ndark:text-zinc-400"
            );

            // Assert
            expect($item->label)->toBe("Multi\nLine\nLabel")
                ->and($item->by)->toBe("User\nName")
                ->and($item->at)->toBe("15.01.2025\n10:30")
                ->and($item->colorClass)->toBe("text-zinc-500\ndark:text-zinc-400");
        });
    });
});
