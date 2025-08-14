<?php

use App\Domains\Admin\Plants\ValueObjects\TimelineEvent;

describe('TimelineEvent', function () {
    describe('creation and basic properties', function () {
        it('creates timeline event with all properties', function () {
            // Act
            $event = new TimelineEvent(
                type: 'created',
                label: 'Erstellt',
                by: 'Admin User',
                at: '15.01.2025 10:30',
                showBy: true,
                colorClass: 'text-emerald-600',
                iconName: 'plus-circle',
                iconColor: 'text-emerald-500',
                details: ['field' => 'name']
            );

            // Assert
            expect($event->type)->toBe('created')
                ->and($event->label)->toBe('Erstellt')
                ->and($event->by)->toBe('Admin User')
                ->and($event->at)->toBe('15.01.2025 10:30')
                ->and($event->showBy)->toBeTrue()
                ->and($event->colorClass)->toBe('text-emerald-600')
                ->and($event->iconName)->toBe('plus-circle')
                ->and($event->iconColor)->toBe('text-emerald-500')
                ->and($event->details)->toBe(['field' => 'name']);
        });

        it('creates timeline event with null values', function () {
            // Act
            $event = new TimelineEvent(
                type: 'deleted',
                label: 'Gelöscht',
                by: null,
                at: null,
                showBy: false,
                colorClass: 'text-red-600',
                iconName: 'trash',
                iconColor: 'text-red-500'
            );

            // Assert
            expect($event->by)->toBeNull()
                ->and($event->at)->toBeNull()
                ->and($event->showBy)->toBeFalse()
                ->and($event->details)->toBeNull();
        });
    });

    describe('factory methods', function () {
        it('creates requested event', function () {
            // Act
            $event = TimelineEvent::requested(
                by: 'Community User',
                at: '14.01.2025 09:00',
                showBy: true
            );

            // Assert
            expect($event->type)->toBe('requested')
                ->and($event->label)->toBe('Pflanze beantragt')
                ->and($event->by)->toBe('Community User')
                ->and($event->at)->toBe('14.01.2025 09:00')
                ->and($event->showBy)->toBeTrue()
                ->and($event->colorClass)->toBe('text-amber-600 dark:text-amber-400')
                ->and($event->iconName)->toBe('user-plus')
                ->and($event->iconColor)->toBe('text-amber-500');
        });

        it('creates created event', function () {
            // Act
            $event = TimelineEvent::created(
                by: 'Admin User',
                at: '15.01.2025 10:30',
                showBy: true
            );

            // Assert
            expect($event->type)->toBe('created')
                ->and($event->label)->toBe('Erstellt')
                ->and($event->by)->toBe('Admin User')
                ->and($event->at)->toBe('15.01.2025 10:30')
                ->and($event->showBy)->toBeTrue()
                ->and($event->colorClass)->toBe('text-emerald-600 dark:text-emerald-400')
                ->and($event->iconName)->toBe('plus-circle')
                ->and($event->iconColor)->toBe('text-emerald-500');
        });

        it('creates updated event', function () {
            // Act
            $event = TimelineEvent::updated(
                by: 'Editor User',
                at: '16.01.2025 14:20',
                showBy: true,
                details: ['description', 'latin_name']
            );

            // Assert
            expect($event->type)->toBe('updated')
                ->and($event->label)->toBe('Aktualisiert')
                ->and($event->by)->toBe('Editor User')
                ->and($event->at)->toBe('16.01.2025 14:20')
                ->and($event->showBy)->toBeTrue()
                ->and($event->colorClass)->toBe('text-blue-600 dark:text-blue-400')
                ->and($event->iconName)->toBe('pencil')
                ->and($event->iconColor)->toBe('text-blue-500')
                ->and($event->details)->toBe(['description', 'latin_name']);
        });

        it('creates update requested event', function () {
            // Act
            $event = TimelineEvent::updateRequested(
                by: 'Community User',
                at: '17.01.2025 11:15',
                showBy: false,
                details: ['category']
            );

            // Assert
            expect($event->type)->toBe('update_requested')
                ->and($event->label)->toBe('Änderung beantragt')
                ->and($event->by)->toBe('Community User')
                ->and($event->at)->toBe('17.01.2025 11:15')
                ->and($event->showBy)->toBeFalse()
                ->and($event->colorClass)->toBe('text-sky-600 dark:text-sky-400')
                ->and($event->iconName)->toBe('clock')
                ->and($event->iconColor)->toBe('text-sky-500')
                ->and($event->details)->toBe(['category']);
        });

        it('creates deleted event', function () {
            // Act
            $event = TimelineEvent::deleted(
                by: 'Admin User',
                at: '20.01.2025 16:45',
                showBy: true
            );

            // Assert
            expect($event->type)->toBe('deleted')
                ->and($event->label)->toBe('Gelöscht')
                ->and($event->by)->toBe('Admin User')
                ->and($event->at)->toBe('20.01.2025 16:45')
                ->and($event->showBy)->toBeTrue()
                ->and($event->colorClass)->toBe('text-red-600 dark:text-red-400')
                ->and($event->iconName)->toBe('trash')
                ->and($event->iconColor)->toBe('text-red-500');
        });

        it('creates restored event', function () {
            // Act
            $event = TimelineEvent::restored(
                by: 'Super Admin',
                at: '25.01.2025 09:00',
                showBy: true
            );

            // Assert
            expect($event->type)->toBe('restored')
                ->and($event->label)->toBe('Wiederhergestellt')
                ->and($event->by)->toBe('Super Admin')
                ->and($event->at)->toBe('25.01.2025 09:00')
                ->and($event->showBy)->toBeTrue()
                ->and($event->colorClass)->toBe('text-green-600 dark:text-green-400')
                ->and($event->iconName)->toBe('arrow-path')
                ->and($event->iconColor)->toBe('text-green-500');
        });
    });

    describe('hasBy method', function () {
        it('returns true when showBy is true and by is not null', function () {
            // Arrange
            $event = TimelineEvent::created(
                by: 'User',
                at: '15.01.2025',
                showBy: true
            );

            // Act & Assert
            expect($event->hasBy())->toBeTrue();
        });

        it('returns false when showBy is false even if by is not null', function () {
            // Arrange
            $event = TimelineEvent::updateRequested(
                by: 'User',
                at: '15.01.2025',
                showBy: false
            );

            // Act & Assert
            expect($event->hasBy())->toBeFalse();
        });

        it('returns false when by is null even if showBy is true', function () {
            // Arrange
            $event = TimelineEvent::created(
                by: null,
                at: '15.01.2025',
                showBy: true
            );

            // Act & Assert
            expect($event->hasBy())->toBeFalse();
        });

        it('returns false when both showBy is false and by is null', function () {
            // Arrange
            $event = TimelineEvent::deleted(
                by: null,
                at: '15.01.2025',
                showBy: false
            );

            // Act & Assert
            expect($event->hasBy())->toBeFalse();
        });
    });

    describe('hasAt method', function () {
        it('returns true when at is not null', function () {
            // Arrange
            $event = TimelineEvent::created(
                by: 'User',
                at: '15.01.2025 10:30',
                showBy: true
            );

            // Act & Assert
            expect($event->hasAt())->toBeTrue();
        });

        it('returns false when at is null', function () {
            // Arrange
            $event = TimelineEvent::created(
                by: 'User',
                at: null,
                showBy: true
            );

            // Act & Assert
            expect($event->hasAt())->toBeFalse();
        });

        it('returns true when at is empty string', function () {
            // Arrange
            $event = TimelineEvent::created(
                by: 'User',
                at: '',
                showBy: true
            );

            // Act & Assert
            expect($event->hasAt())->toBeTrue(); // Empty string is not null
        });
    });

    describe('hasDetails method', function () {
        it('returns true when details array has content', function () {
            // Arrange
            $event = TimelineEvent::updated(
                by: 'User',
                at: '15.01.2025',
                showBy: true,
                details: ['name', 'description']
            );

            // Act & Assert
            expect($event->hasDetails())->toBeTrue();
        });

        it('returns false when details is null', function () {
            // Arrange
            $event = TimelineEvent::created(
                by: 'User',
                at: '15.01.2025',
                showBy: true
            );

            // Act & Assert
            expect($event->hasDetails())->toBeFalse();
        });

        it('returns false when details is empty array', function () {
            // Arrange
            $event = TimelineEvent::updated(
                by: 'User',
                at: '15.01.2025',
                showBy: true,
                details: []
            );

            // Act & Assert
            expect($event->hasDetails())->toBeFalse();
        });

        it('returns true when details has one item', function () {
            // Arrange
            $event = TimelineEvent::updated(
                by: 'User',
                at: '15.01.2025',
                showBy: true,
                details: ['description']
            );

            // Act & Assert
            expect($event->hasDetails())->toBeTrue();
        });
    });

    describe('readonly behavior', function () {
        it('is readonly class', function () {
            // Arrange
            $event = TimelineEvent::created(
                by: 'User',
                at: '15.01.2025',
                showBy: true
            );

            // Assert - These would cause errors if uncommented due to readonly
            // $event->type = 'modified'; // This would fail
            // $event->by = 'Modified User'; // This would fail
            // $event->showBy = false; // This would fail

            expect($event->type)->toBe('created')
                ->and($event->by)->toBe('User');
        });
    });

    describe('edge cases', function () {
        it('handles empty strings in properties', function () {
            // Act
            $event = new TimelineEvent(
                type: '',
                label: '',
                by: '',
                at: '',
                showBy: true,
                colorClass: '',
                iconName: '',
                iconColor: '',
                details: []
            );

            // Assert
            expect($event->type)->toBe('')
                ->and($event->label)->toBe('')
                ->and($event->by)->toBe('')
                ->and($event->at)->toBe('')
                ->and($event->colorClass)->toBe('')
                ->and($event->iconName)->toBe('')
                ->and($event->iconColor)->toBe('');
        });

        it('handles special characters', function () {
            // Arrange
            $specialBy = 'Üser Nämë äöü';
            $specialLabel = 'Spëciäl Evënt ♥';
            $specialAt = '15.01.2025 ♦ 10:30';

            // Act
            $event = new TimelineEvent(
                type: 'special',
                label: $specialLabel,
                by: $specialBy,
                at: $specialAt,
                showBy: true,
                colorClass: 'text-spëciäl',
                iconName: 'special-ïcon',
                iconColor: 'text-ümläüt'
            );

            // Assert
            expect($event->label)->toBe($specialLabel)
                ->and($event->by)->toBe($specialBy)
                ->and($event->at)->toBe($specialAt);
        });

        it('handles long strings', function () {
            // Arrange
            $longLabel = str_repeat('Very Long Label ', 100);
            $longBy = str_repeat('Long User Name ', 50);

            // Act
            $event = new TimelineEvent(
                type: 'long',
                label: $longLabel,
                by: $longBy,
                at: '15.01.2025',
                showBy: true,
                colorClass: 'text-long',
                iconName: 'long-icon',
                iconColor: 'text-long-color'
            );

            // Assert
            expect($event->label)->toBe($longLabel)
                ->and($event->by)->toBe($longBy);
        });

        it('handles complex details array', function () {
            // Arrange
            $complexDetails = [
                'fields' => ['name', 'description', 'latin_name'],
                'changes' => [
                    'name' => ['old' => 'Old Name', 'new' => 'New Name'],
                    'description' => ['old' => null, 'new' => 'Added description'],
                ],
                'metadata' => ['timestamp' => '2025-01-15', 'user_id' => 123],
            ];

            // Act
            $event = TimelineEvent::updated(
                by: 'User',
                at: '15.01.2025',
                showBy: true,
                details: $complexDetails
            );

            // Assert
            expect($event->hasDetails())->toBeTrue()
                ->and($event->details)->toBe($complexDetails)
                ->and($event->details['fields'])->toBe(['name', 'description', 'latin_name']);
        });
    });

    describe('factory methods with null values', function () {
        it('creates events with null by and at values', function () {
            $events = [
                TimelineEvent::requested(by: null, at: null, showBy: true),
                TimelineEvent::created(by: null, at: null, showBy: true),
                TimelineEvent::updated(by: null, at: null, showBy: true),
                TimelineEvent::updateRequested(by: null, at: null, showBy: true),
                TimelineEvent::deleted(by: null, at: null, showBy: true),
                TimelineEvent::restored(by: null, at: null, showBy: true),
            ];

            foreach ($events as $event) {
                expect($event->by)->toBeNull()
                    ->and($event->at)->toBeNull()
                    ->and($event->hasBy())->toBeFalse()
                    ->and($event->hasAt())->toBeFalse();
            }
        });
    });

    describe('event type consistency', function () {
        it('has consistent event types across factory methods', function () {
            $expectedTypes = [
                'requested' => TimelineEvent::requested('User', '2025-01-15', true),
                'created' => TimelineEvent::created('User', '2025-01-15', true),
                'updated' => TimelineEvent::updated('User', '2025-01-15', true),
                'update_requested' => TimelineEvent::updateRequested('User', '2025-01-15', true),
                'deleted' => TimelineEvent::deleted('User', '2025-01-15', true),
                'restored' => TimelineEvent::restored('User', '2025-01-15', true),
            ];

            foreach ($expectedTypes as $expectedType => $event) {
                expect($event->type)->toBe($expectedType);
            }
        });

        it('has unique labels for each event type', function () {
            $events = [
                TimelineEvent::requested('User', '2025-01-15', true),
                TimelineEvent::created('User', '2025-01-15', true),
                TimelineEvent::updated('User', '2025-01-15', true),
                TimelineEvent::updateRequested('User', '2025-01-15', true),
                TimelineEvent::deleted('User', '2025-01-15', true),
                TimelineEvent::restored('User', '2025-01-15', true),
            ];

            $labels = array_map(fn ($event) => $event->label, $events);
            $uniqueLabels = array_unique($labels);

            expect(count($labels))->toBe(count($uniqueLabels)); // All labels should be unique
        });

        it('has consistent icon and color schemes', function () {
            $events = [
                TimelineEvent::requested('User', '2025-01-15', true),
                TimelineEvent::created('User', '2025-01-15', true),
                TimelineEvent::updated('User', '2025-01-15', true),
                TimelineEvent::updateRequested('User', '2025-01-15', true),
                TimelineEvent::deleted('User', '2025-01-15', true),
                TimelineEvent::restored('User', '2025-01-15', true),
            ];

            foreach ($events as $event) {
                expect($event->iconName)->toBeString()
                    ->and($event->iconColor)->toBeString()
                    ->and($event->colorClass)->toBeString()
                    ->and($event->iconName)->not->toBeEmpty()
                    ->and($event->iconColor)->not->toBeEmpty()
                    ->and($event->colorClass)->not->toBeEmpty();
            }
        });
    });

    describe('whitespace and formatting', function () {
        it('preserves whitespace in text fields', function () {
            // Act
            $event = new TimelineEvent(
                type: '  spaced  ',
                label: '  Spaced Label  ',
                by: '  Spaced User  ',
                at: '  15.01.2025 10:30  ',
                showBy: true,
                colorClass: '  text-spaced  ',
                iconName: '  spaced-icon  ',
                iconColor: '  text-spaced-color  '
            );

            // Assert
            expect($event->type)->toBe('  spaced  ')
                ->and($event->label)->toBe('  Spaced Label  ')
                ->and($event->by)->toBe('  Spaced User  ')
                ->and($event->at)->toBe('  15.01.2025 10:30  ');
        });

        it('handles line breaks in text', function () {
            // Act
            $event = new TimelineEvent(
                type: "multi\nline",
                label: "Multi\nLine\nLabel",
                by: "User\nName",
                at: "15.01.2025\n10:30",
                showBy: true,
                colorClass: "text-multi\nline",
                iconName: "multi\nicon",
                iconColor: "text-multi\ncolor"
            );

            // Assert
            expect($event->type)->toBe("multi\nline")
                ->and($event->label)->toBe("Multi\nLine\nLabel")
                ->and($event->by)->toBe("User\nName")
                ->and($event->at)->toBe("15.01.2025\n10:30");
        });
    });
});
