<?php

// database/seeders/PlantSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plant;
use App\Models\PlantTimelineProjection;
use Illuminate\Support\Facades\DB;

class PlantSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $this->createVegetableGarden();
            $this->createHerbGarden();
            $this->createFlowerGarden();
            $this->createCommunityContributions();
            $this->createDeletedPlants();
        });
    }

    private function createVegetableGarden(): void
    {
        $this->command->info('🥕 Creating vegetable garden...');

        // Featured vegetable with rich timeline
        $roteBete = Plant::factory()
            ->vegetable()
            ->withCompleteData()
            ->communityRequested()
            ->create([
                'name' => 'Rote Beete',
                'category' => 'Wurzelgemüse',
                'latin_name' => 'Beta vulgaris',
                'description' => 'Die Rote Beete ist eine Wurzelgemüseart, die für ihre leuchtend rote Farbe bekannt ist. Sie wird oft in Salaten, Suppen und als Beilage verwendet. Reich an Vitaminen und Mineralien, ist sie auch für ihre gesundheitsfördernden Eigenschaften bekannt.',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/21/Beets-Bundle.jpg/330px-Beets-Bundle.jpg',
            ]);

        $this->createRichTimeline($roteBete);

        // Other vegetables
        $vegetables = [
            ['name' => 'Karotten', 'category' => 'Wurzelgemüse', 'latin' => 'Daucus carota'],
            ['name' => 'Tomaten', 'category' => 'Fruchtgemüse', 'latin' => 'Solanum lycopersicum'],
            ['name' => 'Gurken', 'category' => 'Fruchtgemüse', 'latin' => 'Cucumis sativus'],
            ['name' => 'Paprika', 'category' => 'Fruchtgemüse', 'latin' => 'Capsicum annuum'],
            ['name' => 'Zwiebeln', 'category' => 'Zwiebelgemüse', 'latin' => 'Allium cepa'],
            ['name' => 'Brokkoli', 'category' => 'Kohlgemüse', 'latin' => 'Brassica oleracea'],
        ];

        foreach ($vegetables as $veggie) {
            $plant = Plant::factory()
                ->vegetable()
                ->withImage()
                ->create([
                    'name' => $veggie['name'],
                    'category' => $veggie['category'],
                    'latin_name' => $veggie['latin'],
                ]);

            $this->createBasicTimeline($plant);
        }
    }

    private function createHerbGarden(): void
    {
        $this->command->info('🌿 Creating herb garden...');

        $herbs = [
            ['name' => 'Basilikum', 'latin' => 'Ocimum basilicum'],
            ['name' => 'Petersilie', 'latin' => 'Petroselinum crispum'],
            ['name' => 'Rosmarin', 'latin' => 'Rosmarinus officinalis'],
            ['name' => 'Thymian', 'latin' => 'Thymus vulgaris'],
            ['name' => 'Oregano', 'latin' => 'Origanum vulgare'],
        ];

        foreach ($herbs as $herb) {
            $plant = Plant::factory()
                ->herb()
                ->create([
                    'name' => $herb['name'],
                    'latin_name' => $herb['latin'],
                ]);

            $this->createBasicTimeline($plant, rand(1, 3) == 1); // 33% chance for community request
        }
    }

    private function createFlowerGarden(): void
    {
        $this->command->info('🌸 Creating flower garden...');

        Plant::factory()
            ->count(8)
            ->flower()
            ->withImage()
            ->create()
            ->each(function ($plant) {
                $this->createBasicTimeline($plant);
            });
    }

    private function createCommunityContributions(): void
    {
        $this->command->info('👥 Creating community contributions...');

        // Plants with lots of community activity
        Plant::factory()
            ->count(5)
            ->vegetable()
            ->communityRequested()
            ->create()
            ->each(function ($plant) {
                $this->createCommunityTimeline($plant);
            });
    }

    private function createDeletedPlants(): void
    {
        $this->command->info('🗑️ Creating deleted plants...');

        // Some deleted plants
        Plant::factory()
            ->count(3)
            ->vegetable()
            ->deleted()
            ->create()
            ->each(function ($plant) {
                $this->createDeletedTimeline($plant);
            });
    }

    private function createRichTimeline(Plant $plant): void
    {
        $events = [
            // 1. Community request
            ['type' => 'requested', 'sequence' => 1, 'days_ago' => 10],
            // 2. Created by admin
            ['type' => 'created', 'sequence' => 2, 'days_ago' => 5],
            // 3. Community suggests description
            ['type' => 'updateRequested', 'sequence' => 3, 'days_ago' => 4],
            // 4. Community suggests latin name
            ['type' => 'updateRequested', 'sequence' => 4, 'days_ago' => 4],
            // 5. Admin approves description
            ['type' => 'updated', 'sequence' => 5, 'days_ago' => 3],
            // 6. Community suggests category correction
            ['type' => 'updateRequested', 'sequence' => 6, 'days_ago' => 2],
            // 7. Admin makes final update
            ['type' => 'updated', 'sequence' => 7, 'days_ago' => 1],
        ];

        foreach ($events as $event) {
            PlantTimelineProjection::factory()
                ->{$event['type']}()
                ->forPlant($plant)
                ->withSequence($event['sequence'])
                ->create([
                    'performed_at' => now()->subDays($event['days_ago']),
                ]);
        }
    }

    private function createBasicTimeline(Plant $plant, bool $hasCommunityRequest = false): void
    {
        $sequence = 1;

        if ($hasCommunityRequest) {
            PlantTimelineProjection::factory()
                ->requested()
                ->forPlant($plant)
                ->withSequence($sequence++)
                ->create([
                    'performed_at' => now()->subDays(rand(30, 90)),
                ]);
        }

        // Creation event
        PlantTimelineProjection::factory()
            ->created()
            ->forPlant($plant)
            ->withSequence($sequence++)
            ->create([
                'performed_at' => now()->subDays(rand(7, 30)),
            ]);

        // Random updates
        for ($i = 0; $i < rand(1, 4); $i++) {
            if (rand(1, 3) == 1) { // 33% chance for update request
                PlantTimelineProjection::factory()
                    ->updateRequested()
                    ->forPlant($plant)
                    ->withSequence($sequence++)
                    ->create([
                        'performed_at' => now()->subDays(rand(1, 7)),
                    ]);
            }

            if (rand(1, 2) == 1) { // 50% chance for actual update
                PlantTimelineProjection::factory()
                    ->updated()
                    ->forPlant($plant)
                    ->withSequence($sequence++)
                    ->create([
                        'performed_at' => now()->subDays(rand(1, 5)),
                    ]);
            }
        }
    }

    private function createCommunityTimeline(Plant $plant): void
    {
        $sequence = 1;
        $currentDate = now()->subDays(60);

        // 1. Community request
        PlantTimelineProjection::factory()
            ->requested()
            ->forPlant($plant)
            ->withSequence($sequence++)
            ->create(['performed_at' => $currentDate]);

        $currentDate = $currentDate->addDays(rand(3, 7));

        // 2. Created by admin
        PlantTimelineProjection::factory()
            ->created()
            ->forPlant($plant)
            ->withSequence($sequence++)
            ->create(['performed_at' => $currentDate]);

        // 3-8. Multiple community contributions
        for ($i = 0; $i < rand(5, 10); $i++) {
            $currentDate = $currentDate->addDays(rand(1, 5));

            if (rand(1, 3) == 1) { // Community suggestion
                PlantTimelineProjection::factory()
                    ->updateRequested()
                    ->forPlant($plant)
                    ->withSequence($sequence++)
                    ->create(['performed_at' => $currentDate]);
            } else { // Admin update
                PlantTimelineProjection::factory()
                    ->updated()
                    ->forPlant($plant)
                    ->withSequence($sequence++)
                    ->create(['performed_at' => $currentDate]);
            }
        }
    }

    private function createDeletedTimeline(Plant $plant): void
    {
        $sequence = 1;

        // Created
        PlantTimelineProjection::factory()
            ->created()
            ->forPlant($plant)
            ->withSequence($sequence++)
            ->create([
                'performed_at' => now()->subDays(rand(30, 60)),
            ]);

        // Maybe some updates
        for ($i = 0; $i < rand(1, 3); $i++) {
            PlantTimelineProjection::factory()
                ->updated()
                ->forPlant($plant)
                ->withSequence($sequence++)
                ->create([
                    'performed_at' => now()->subDays(rand(7, 20)),
                ]);
        }

        // Deleted
        PlantTimelineProjection::factory()
            ->deleted()
            ->forPlant($plant)
            ->withSequence($sequence++)
            ->create([
                'performed_at' => now()->subDays(rand(1, 7)),
            ]);

        // Some might be restored
        if (rand(1, 3) == 1) {
            PlantTimelineProjection::factory()
                ->restored()
                ->forPlant($plant)
                ->withSequence($sequence++)
                ->create([
                    'performed_at' => now()->subDays(rand(1, 3)),
                ]);

            // Update plant status
            $plant->update(['is_deleted' => false]);
        }
    }
}
