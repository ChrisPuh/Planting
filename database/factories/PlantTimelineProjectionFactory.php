<?php

// database/factories/PlantTimelineProjectionFactory.php

namespace Database\Factories;

use App\Models\Plant;
use App\Models\PlantTimelineProjection;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlantTimelineProjectionFactory extends Factory
{
    protected $model = PlantTimelineProjection::class;

    private static array $usernames = [
        'GartenProfi_Max', 'BlumenLiebhaberin_Anna', 'Kräuter_Klaus', 'Gemüse_Greta',
        'PflanzenfanTom', 'BioBauer_Ben', 'HobbygärtnerLisa', 'Botaniker_Bob',
        'GrünerDaumen_Gabi', 'Pflanzenflüsterer_Paul', 'Admin_User', 'Community_Helper',
        'Garten_Experte', 'Pflanzendoktor_Petra', 'Seedling_Sam',
    ];

    public function definition(): array
    {
        return [
            'plant_uuid' => Plant::factory(),
            'event_type' => $this->faker->randomElement(['created', 'updated', 'update_requested']),
            'performed_by' => $this->faker->randomElement(self::$usernames),
            'performed_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'event_details' => [],
            'display_text' => null,
            'sequence_number' => $this->faker->numberBetween(1, 20),
        ];
    }

    // Event Type States
    public function requested(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'event_type' => 'requested',
                'performed_by' => $this->faker->randomElement(array_filter(self::$usernames, fn ($u) => $u !== 'Admin_User')),
                'event_details' => [
                    'plant_name' => $this->faker->randomElement(['Neue Tomatensorte', 'Exotische Blume', 'Seltenes Kraut']),
                    'reason' => 'Community-Anfrage für neue Pflanze',
                ],
                'display_text' => 'hat eine neue Pflanze beantragt',
            ];
        });
    }

    public function created(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'event_type' => 'created',
                'performed_by' => $this->faker->randomElement(['Admin_User', 'Garten_Experte']),
                'event_details' => [
                    'initial_data' => [
                        'name' => 'Neue Pflanze',
                        'type' => $this->faker->randomElement(['gemuese', 'blume', 'kraeuter']),
                    ],
                ],
                'display_text' => 'hat die Pflanze erstellt',
            ];
        });
    }

    public function updated(): static
    {
        return $this->state(function (array $attributes) {
            $changedFields = $this->faker->randomElements(
                ['description', 'category', 'latin_name', 'image_url'],
                $this->faker->numberBetween(1, 3)
            );

            return [
                'event_type' => 'updated',
                'performed_by' => $this->faker->randomElement(['Admin_User', 'Garten_Experte', 'Botaniker_Bob']),
                'event_details' => [
                    'changed_fields' => $changedFields,
                    'changes' => array_combine(
                        $changedFields,
                        array_map(fn () => $this->faker->sentence(), $changedFields)
                    ),
                ],
                'display_text' => 'hat '.implode(', ', $changedFields).' aktualisiert',
            ];
        });
    }

    public function updateRequested(): static
    {
        return $this->state(function (array $attributes) {
            $requestedFields = $this->faker->randomElements(
                ['description', 'category', 'latin_name'],
                $this->faker->numberBetween(1, 2)
            );

            return [
                'event_type' => 'update_requested',
                'performed_by' => $this->faker->randomElement(array_filter(self::$usernames, fn ($u) => ! str_contains($u, 'Admin'))),
                'event_details' => [
                    'requested_fields' => $requestedFields,
                    'proposed_changes' => array_combine(
                        $requestedFields,
                        array_map(fn () => $this->faker->sentence(), $requestedFields)
                    ),
                    'reason' => $this->faker->randomElement([
                        'Fehlerkorrektur',
                        'Zusätzliche Information',
                        'Verbesserung der Beschreibung',
                        'Aktualisierung der Kategorie',
                    ]),
                ],
                'display_text' => 'hat Änderungen für '.implode(', ', $requestedFields).' vorgeschlagen',
            ];
        });
    }

    public function deleted(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'event_type' => 'deleted',
                'performed_by' => 'Admin_User',
                'event_details' => [
                    'reason' => $this->faker->randomElement([
                        'Duplikat entfernt',
                        'Falsche Information',
                        'Nicht relevant',
                        'Community-Anfrage',
                    ]),
                ],
                'display_text' => 'hat die Pflanze gelöscht',
            ];
        });
    }

    public function restored(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'event_type' => 'restored',
                'performed_by' => 'Admin_User',
                'event_details' => [
                    'reason' => $this->faker->randomElement([
                        'Löschung war ein Fehler',
                        'Community-Anfrage zur Wiederherstellung',
                        'Neue Informationen verfügbar',
                    ]),
                ],
                'display_text' => 'hat die Pflanze wiederhergestellt',
            ];
        });
    }

    // Helper States
    public function forPlant(\App\Models\Plant $plant): static
    {
        return $this->state(function (array $attributes) use ($plant) {
            return [
                'plant_uuid' => $plant->uuid,
            ];
        });
    }

    public function withSequence(int $sequence): static
    {
        return $this->state(function (array $attributes) use ($sequence) {
            return [
                'sequence_number' => $sequence,
            ];
        });
    }

    public function recentEvent(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'performed_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            ];
        });
    }

    public function oldEvent(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'performed_at' => $this->faker->dateTimeBetween('-6 months', '-1 month'),
            ];
        });
    }
}
