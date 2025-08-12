<?php

namespace Database\Factories;

use App\Models\Plant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Plant>
 */
class PlantFactory extends Factory
{
    private static array $vegetables = [
        ['name' => 'Rote Beete', 'category' => 'Wurzelgemüse', 'latin' => 'Beta vulgaris'],
        ['name' => 'Karotten', 'category' => 'Wurzelgemüse', 'latin' => 'Daucus carota'],
        ['name' => 'Tomaten', 'category' => 'Fruchtgemüse', 'latin' => 'Solanum lycopersicum'],
        ['name' => 'Gurken', 'category' => 'Fruchtgemüse', 'latin' => 'Cucumis sativus'],
        ['name' => 'Salat', 'category' => 'Blattgemüse', 'latin' => 'Lactuca sativa'],
        ['name' => 'Spinat', 'category' => 'Blattgemüse', 'latin' => 'Spinacia oleracea'],
        ['name' => 'Paprika', 'category' => 'Fruchtgemüse', 'latin' => 'Capsicum annuum'],
        ['name' => 'Zwiebeln', 'category' => 'Zwiebelgemüse', 'latin' => 'Allium cepa'],
        ['name' => 'Knoblauch', 'category' => 'Zwiebelgemüse', 'latin' => 'Allium sativum'],
        ['name' => 'Brokkoli', 'category' => 'Kohlgemüse', 'latin' => 'Brassica oleracea'],
        ['name' => 'Blumenkohl', 'category' => 'Kohlgemüse', 'latin' => 'Brassica oleracea'],
        ['name' => 'Rosenkohl', 'category' => 'Kohlgemüse', 'latin' => 'Brassica oleracea'],
        ['name' => 'Radieschen', 'category' => 'Wurzelgemüse', 'latin' => 'Raphanus sativus'],
        ['name' => 'Sellerie', 'category' => 'Wurzelgemüse', 'latin' => 'Apium graveolens'],
        ['name' => 'Lauch', 'category' => 'Zwiebelgemüse', 'latin' => 'Allium porrum'],
    ];

    private static array $herbs = [
        ['name' => 'Basilikum', 'category' => 'Kräuter', 'latin' => 'Ocimum basilicum'],
        ['name' => 'Petersilie', 'category' => 'Kräuter', 'latin' => 'Petroselinum crispum'],
        ['name' => 'Rosmarin', 'category' => 'Kräuter', 'latin' => 'Rosmarinus officinalis'],
        ['name' => 'Thymian', 'category' => 'Kräuter', 'latin' => 'Thymus vulgaris'],
        ['name' => 'Oregano', 'category' => 'Kräuter', 'latin' => 'Origanum vulgare'],
        ['name' => 'Schnittlauch', 'category' => 'Kräuter', 'latin' => 'Allium schoenoprasum'],
        ['name' => 'Dill', 'category' => 'Kräuter', 'latin' => 'Anethum graveolens'],
        ['name' => 'Salbei', 'category' => 'Kräuter', 'latin' => 'Salvia officinalis'],
    ];

    private static array $flowers = [
        ['name' => 'Sonnenblume', 'category' => 'Einjährige', 'latin' => 'Helianthus annuus'],
        ['name' => 'Rose', 'category' => 'Stauden', 'latin' => 'Rosa'],
        ['name' => 'Tulpe', 'category' => 'Zwiebelpflanzen', 'latin' => 'Tulipa'],
        ['name' => 'Narzisse', 'category' => 'Zwiebelpflanzen', 'latin' => 'Narcissus'],
        ['name' => 'Lavendel', 'category' => 'Stauden', 'latin' => 'Lavandula'],
        ['name' => 'Ringelblume', 'category' => 'Einjährige', 'latin' => 'Calendula officinalis'],
        ['name' => 'Vergissmeinnicht', 'category' => 'Zweijährige', 'latin' => 'Myosotis'],
        ['name' => 'Dahlie', 'category' => 'Knollenpflanzen', 'latin' => 'Dahlia'],
    ];

    private static array $usernames = [
        'GartenProfi_Max', 'BlumenLiebhaberin_Anna', 'Kräuter_Klaus', 'Gemüse_Greta',
        'PflanzenfanTom', 'BioBauer_Ben', 'HobbygärtnerLisa', 'Botaniker_Bob',
        'GrünerDaumen_Gabi', 'Pflanzenflüsterer_Paul', 'Admin_User', 'Community_Helper',
        'Garten_Experte', 'Pflanzendoktor_Petra', 'Seedling_Sam'
    ];

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'name' => $this->faker->randomElement(['Rote Beete', 'Karotten', 'Tomaten']),
            'type' => 'gemuese',
            'category' => 'Wurzelgemüse',
            'latin_name' => null,
            'description' => null,
            'image_url' => null,
            'is_deleted' => false,
            'was_community_requested' => $this->faker->boolean(30), // 30% community requested
            'created_by' => $this->faker->randomElement(['Admin_User', 'Garten_Experte']),
            'last_updated_by' => $this->faker->randomElement(self::$usernames),
            'last_event_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
        ];
    }

    // States
    public function vegetable(): static
    {
        return $this->state(function (array $attributes) {
            $vegetable = $this->faker->randomElement(self::$vegetables);
            return [
                'name' => $vegetable['name'],
                'type' => 'gemuese',
                'category' => $vegetable['category'],
                'latin_name' => $this->faker->boolean(70) ? $vegetable['latin'] : null,
                'description' => $this->generateDescription($vegetable['name'], 'Gemüse'),
            ];
        });
    }

    public function herb(): static
    {
        return $this->state(function (array $attributes) {
            $herb = $this->faker->randomElement(self::$herbs);
            return [
                'name' => $herb['name'],
                'type' => 'kraeuter',
                'category' => $herb['category'],
                'latin_name' => $this->faker->boolean(80) ? $herb['latin'] : null,
                'description' => $this->generateDescription($herb['name'], 'Kraut'),
            ];
        });
    }

    public function flower(): static
    {
        return $this->state(function (array $attributes) {
            $flower = $this->faker->randomElement(self::$flowers);
            return [
                'name' => $flower['name'],
                'type' => 'blume',
                'category' => $flower['category'],
                'latin_name' => $this->faker->boolean(60) ? $flower['latin'] : null,
                'description' => $this->generateDescription($flower['name'], 'Blume'),
            ];
        });
    }

    public function withImage(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'image_url' => $this->faker->imageUrl(400, 300, 'plants', true, $attributes['name']),
            ];
        });
    }

    public function communityRequested(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'was_community_requested' => true,
                'created_by' => 'Admin_User', // Admin created after community request
            ];
        });
    }

    public function deleted(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'is_deleted' => true,
                'last_updated_by' => 'Admin_User',
                'last_event_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            ];
        });
    }

    public function withCompleteData(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'latin_name' => $attributes['latin_name'] ?? 'Plantus exampleus',
                'description' => $attributes['description'] ?? $this->generateDescription($attributes['name'], 'Pflanze'),
                'image_url' => $this->faker->imageUrl(400, 300, 'plants', true),
            ];
        });
    }

    private function generateDescription(string $plantName, string $type): string
    {
        $descriptions = [
            'gemuese' => [
                "ist ein beliebtes Gemüse, das reich an Vitaminen und Mineralien ist.",
                "wird häufig in der Küche verwendet und ist sehr gesund.",
                "ist ein nahrhaftes Gemüse, das in vielen Gärten angebaut wird.",
                "eignet sich hervorragend für den Anbau im eigenen Garten.",
            ],
            'kraut' => [
                "ist ein aromatisches Kraut, das häufig in der Küche verwendet wird.",
                "hat einen charakteristischen Geschmack und wird gerne zum Würzen verwendet.",
                "ist ein beliebtes Küchenkraut mit vielen gesundheitlichen Vorteilen.",
                "verleiht Gerichten einen besonderen Geschmack.",
            ],
            'blume' => [
                "ist eine wunderschöne Blume, die jeden Garten verschönert.",
                "blüht in prächtigen Farben und ist bei Gärtnern sehr beliebt.",
                "ist eine attraktive Blume, die Bienen und Schmetterlinge anzieht.",
                "eignet sich perfekt für Blumenbeete und als Schnittblume.",
            ],
        ];

        $typeKey = match ($type) {
            'Gemüse' => 'gemuese',
            'Kraut' => 'kraut',
            'Blume' => 'blume',
            default => 'gemuese'
        };

        $description = $this->faker->randomElement($descriptions[$typeKey]);

        return "{$plantName} {$description} " . $this->faker->sentence();
    }
}
