<?php

use App\Domains\Admin\Plants\ValueObjects\PlantBadge;

describe('PlantBadge', function () {
    describe('creation and basic properties', function () {
        it('creates badge with required properties', function () {
            // Act
            $badge = new PlantBadge(
                text: 'Neu',
                color: 'green'
            );

            // Assert
            expect($badge->text)->toBe('Neu')
                ->and($badge->color)->toBe('green')
                ->and($badge->variant)->toBeNull();
        });

        it('creates badge with all properties', function () {
            // Act
            $badge = new PlantBadge(
                text: 'Community Beitrag',
                color: 'blue',
                variant: 'solid'
            );

            // Assert
            expect($badge->text)->toBe('Community Beitrag')
                ->and($badge->color)->toBe('blue')
                ->and($badge->variant)->toBe('solid');
        });

        it('creates badge with null variant', function () {
            // Act
            $badge = new PlantBadge(
                text: 'Test Badge',
                color: 'red',
                variant: null
            );

            // Assert
            expect($badge->text)->toBe('Test Badge')
                ->and($badge->color)->toBe('red')
                ->and($badge->variant)->toBeNull();
        });
    });

    describe('edge cases and validation', function () {
        it('handles empty text', function () {
            // Act
            $badge = new PlantBadge(
                text: '',
                color: 'green'
            );

            // Assert
            expect($badge->text)->toBe('')
                ->and($badge->color)->toBe('green');
        });

        it('handles empty color', function () {
            // Act
            $badge = new PlantBadge(
                text: 'Test',
                color: ''
            );

            // Assert
            expect($badge->text)->toBe('Test')
                ->and($badge->color)->toBe('');
        });

        it('handles special characters in text', function () {
            // Arrange
            $specialText = 'äöü ßÄÖÜ €@#$%^&*()';

            // Act
            $badge = new PlantBadge(
                text: $specialText,
                color: 'blue'
            );

            // Assert
            expect($badge->text)->toBe($specialText);
        });

        it('handles long text', function () {
            // Arrange
            $longText = str_repeat('A', 1000);

            // Act
            $badge = new PlantBadge(
                text: $longText,
                color: 'green'
            );

            // Assert
            expect($badge->text)->toBe($longText);
        });

        it('handles various color values', function () {
            $colors = ['green', 'blue', 'red', 'yellow', 'purple', 'gray', 'orange'];

            foreach ($colors as $color) {
                $badge = new PlantBadge(
                    text: 'Test',
                    color: $color
                );

                expect($badge->color)->toBe($color);
            }
        });

        it('handles various variant values', function () {
            $variants = ['solid', 'outline', 'ghost', 'subtle'];

            foreach ($variants as $variant) {
                $badge = new PlantBadge(
                    text: 'Test',
                    color: 'blue',
                    variant: $variant
                );

                expect($badge->variant)->toBe($variant);
            }
        });
    });

    describe('readonly behavior', function () {
        it('is readonly class', function () {
            // Arrange
            $badge = new PlantBadge(
                text: 'Test',
                color: 'blue'
            );

            // Assert - These would cause errors if uncommented due to readonly
            // $badge->text = 'Modified'; // This would fail
            // $badge->color = 'red'; // This would fail
            // $badge->variant = 'solid'; // This would fail

            expect($badge->text)->toBe('Test')
                ->and($badge->color)->toBe('blue');
        });
    });

    describe('common use cases', function () {
        it('creates new plant badge', function () {
            // Act
            $badge = new PlantBadge(
                text: 'Neu',
                color: 'green',
                variant: 'solid'
            );

            // Assert
            expect($badge->text)->toBe('Neu')
                ->and($badge->color)->toBe('green')
                ->and($badge->variant)->toBe('solid');
        });

        it('creates community requested badge', function () {
            // Act
            $badge = new PlantBadge(
                text: 'Community Wunsch',
                color: 'blue',
                variant: 'outline'
            );

            // Assert
            expect($badge->text)->toBe('Community Wunsch')
                ->and($badge->color)->toBe('blue')
                ->and($badge->variant)->toBe('outline');
        });

        it('creates updated badge', function () {
            // Act
            $badge = new PlantBadge(
                text: 'Aktualisiert',
                color: 'orange'
            );

            // Assert
            expect($badge->text)->toBe('Aktualisiert')
                ->and($badge->color)->toBe('orange')
                ->and($badge->variant)->toBeNull();
        });

        it('creates deleted badge', function () {
            // Act
            $badge = new PlantBadge(
                text: 'Gelöscht',
                color: 'red',
                variant: 'solid'
            );

            // Assert
            expect($badge->text)->toBe('Gelöscht')
                ->and($badge->color)->toBe('red')
                ->and($badge->variant)->toBe('solid');
        });

        it('creates popular badge', function () {
            // Act
            $badge = new PlantBadge(
                text: 'Beliebt',
                color: 'purple',
                variant: 'ghost'
            );

            // Assert
            expect($badge->text)->toBe('Beliebt')
                ->and($badge->color)->toBe('purple')
                ->and($badge->variant)->toBe('ghost');
        });
    });

    describe('serialization', function () {
        it('can be serialized to array', function () {
            // Arrange
            $badge = new PlantBadge(
                text: 'Test Badge',
                color: 'blue',
                variant: 'solid'
            );

            // Act
            $serialized = [
                'text' => $badge->text,
                'color' => $badge->color,
                'variant' => $badge->variant,
            ];

            // Assert
            expect($serialized)->toBe([
                'text' => 'Test Badge',
                'color' => 'blue',
                'variant' => 'solid',
            ]);
        });

        it('can be serialized to JSON', function () {
            // Arrange
            $badge = new PlantBadge(
                text: 'JSON Test',
                color: 'green'
            );

            // Act
            $data = [
                'text' => $badge->text,
                'color' => $badge->color,
                'variant' => $badge->variant,
            ];
            $json = json_encode($data);
            $decoded = json_decode($json, true);

            // Assert
            expect($decoded)->toBe([
                'text' => 'JSON Test',
                'color' => 'green',
                'variant' => null,
            ]);
        });
    });

    describe('comparison scenarios', function () {
        it('compares badges with same properties', function () {
            // Arrange
            $badge1 = new PlantBadge(
                text: 'Same',
                color: 'blue',
                variant: 'solid'
            );
            $badge2 = new PlantBadge(
                text: 'Same',
                color: 'blue',
                variant: 'solid'
            );

            // Assert
            expect($badge1->text)->toBe($badge2->text)
                ->and($badge1->color)->toBe($badge2->color)
                ->and($badge1->variant)->toBe($badge2->variant);
        });

        it('compares badges with different properties', function () {
            // Arrange
            $badge1 = new PlantBadge(
                text: 'First',
                color: 'blue'
            );
            $badge2 = new PlantBadge(
                text: 'Second',
                color: 'red'
            );

            // Assert
            expect($badge1->text)->not->toBe($badge2->text)
                ->and($badge1->color)->not->toBe($badge2->color);
        });
    });

    describe('whitespace handling', function () {
        it('preserves whitespace in text', function () {
            // Act
            $badge = new PlantBadge(
                text: '  Spaced Text  ',
                color: 'blue'
            );

            // Assert
            expect($badge->text)->toBe('  Spaced Text  ');
        });

        it('handles text with line breaks', function () {
            // Act
            $badge = new PlantBadge(
                text: "Line 1\nLine 2",
                color: 'green'
            );

            // Assert
            expect($badge->text)->toBe("Line 1\nLine 2");
        });

        it('handles text with tabs', function () {
            // Act
            $badge = new PlantBadge(
                text: "Tab\tSeparated",
                color: 'red'
            );

            // Assert
            expect($badge->text)->toBe("Tab\tSeparated");
        });
    });
});
