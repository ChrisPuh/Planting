<?php

// App\Domains\Admin\Plants\Services\PlantService.php - Clean mit Mapper
namespace App\Domains\Admin\Plants\Services;

use App\Domains\Admin\Plants\ViewModels\Show\PlantViewModel;
use App\Domains\Admin\Plants\Mappers\PlantViewModelMapper;
use App\Domains\Admin\Plants\Mappers\PlantTimelineMapper;

readonly class PlantService
{
    public function __construct(
        private PlantViewModelMapper $viewModelMapper,
        private PlantTimelineMapper  $timelineMapper,
        // private readonly PlantRepository $repository, // Später
    ) {}

    public function getPlantForShow(int $id): PlantViewModel
    {
        // 1. Daten holen (später aus Repository)
        $plantData = $this->getDummyPlantData($id);

        // 2. Timeline Events erstellen (später aus DB)
        $timelineData = $this->timelineMapper->createDummyTimelineEvents($plantData);

        // 3. Zu ViewModel mappen
        return $this->viewModelMapper->toShowViewModel($plantData, $timelineData);
    }

    private function getDummyPlantData(int $id): array
    {
        return [
            'id' => 1,
            'name' => 'Rote Beete',
            'type' => 'Gemüse',
            'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/21/Beets-Bundle.jpg/330px-Beets-Bundle.jpg',
            'category' => 'Wurzelgemüse',
            'latin_name' => null,
            'description' => 'Die Rote Beete ist eine Wurzelgemüseart, die für ihre leuchtend rote Farbe bekannt ist. Sie wird oft in Salaten, Suppen und als Beilage verwendet. Reich an Vitaminen und Mineralien, ist sie auch für ihre gesundheitsfördernden Eigenschaften bekannt.',

            'requested_by' => auth()->user()?->is_admin ? 'Max Mustermann' : null,
            'requested_at' => now()->subDays(10),
            'created_by' => auth()->user()?->is_admin ? 'Admin User' : null,
            'created_at' => now()->subDays(5),
            'updated_by' => auth()->user()?->is_admin ? 'Max Mustermann' : null,
            'updated_at' => now()->subDays(2),
            'deleted_by' => auth()->user()?->is_admin ? 'Admin User' : null,
            'deleted_at' => now()->subDays(1),
        ];
    }
}

// Später mit echten Daten:
/*
public function getPlantForShow(int $id): PlantViewModel
{
    // 1. Daten aus Repository
    $plant = $this->repository->findWithTimeline($id);
    $plantData = $plant->toArray();
    $timelineData = $plant->timelineEvents->toArray();

    // 2. Zu ViewModel mappen
    return $this->viewModelMapper->toShowViewModel($plantData, $timelineData);
}
*/
