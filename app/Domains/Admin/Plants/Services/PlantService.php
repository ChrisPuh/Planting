<?php

// App\Domains\Admin\Plants\Services\PlantService.php - Updated

namespace App\Domains\Admin\Plants\Services;

use App\Domains\Admin\Plants\Contracts\PlantRepositoryInterface;
use App\Domains\Admin\Plants\Mappers\PlantTimelineMapper;
use App\Domains\Admin\Plants\Mappers\PlantViewModelMapper;
use App\Domains\Admin\Plants\ViewModels\Show\PlantViewModel;

readonly class PlantService
{
    public function __construct(
        private PlantRepositoryInterface $repository,
        private PlantViewModelMapper $viewModelMapper,
        private PlantTimelineMapper $timelineMapper,
    ) {}

    public function getPlantForShow(string $plantUuid): PlantViewModel
    {
        // 1. Plant + Timeline aus Repository holen
        $data = $this->repository->findWithTimeline($plantUuid);

        // 2. Timeline Events zu TimelineEvent ValueObjects mappen
        $timelineEvents = $this->timelineMapper->mapTimelineEventsFromDatabase(
            $data['timeline_events']
        );

        // 3. Plant ViewModel erstellen
        return $this->viewModelMapper->toShowViewModel(
            $data['plant'],
            $timelineEvents
        );
    }

    public function getPlantForEdit(string $plantUuid): PlantViewModel
    {
        // Für Edit könnten andere Regeln gelten (z.B. keine Timeline)
        return $this->getPlantForShow($plantUuid);
    }

    public function getPlantsForIndex(?array $filters = null): array
    {
        $plants = $this->repository->getAll($filters);

        return array_map(function ($plant) {
            // Für Index brauchen wir eine einfachere Version
            return $this->viewModelMapper->toIndexViewModel($plant);
        }, $plants);
    }

    public function getAllPlantTypes(): array
    {
        // Helper method für Filter-Dropdown
        return [
            'gemuese' => 'Gemüse',
            'kraeuter' => 'Kräuter',
            'blume' => 'Blumen',
            'strauch' => 'Sträucher',
            'baum' => 'Bäume',
        ];
    }

    public function searchPlants(string $query): array
    {
        return $this->getPlantsForIndex(['search' => $query]);
    }
}
