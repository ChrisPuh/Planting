<?php

namespace App\Domains\Admin\Plants\ViewModels\Index;

readonly class PlantListViewModel
{
    /**
     * @param PlantListItemViewModel[] $plants
     */
    public function __construct(
        public array                  $plants,
        public int                    $total,
        public int                    $currentPage,
        public int                    $perPage,
        public ?PlantFiltersViewModel $filters = null,
    )
    {
    }

    public static function from(array $plantsData, int $total, int $currentPage, int $perPage, ?array $filters = null): self
    {
        $plants = array_map(fn($plant) => PlantListItemViewModel::from($plant), $plantsData);
        $filtersViewModel = $filters ? PlantFiltersViewModel::from($filters) : null;

        return new self($plants, $total, $currentPage, $perPage, $filtersViewModel);
    }

    public function hasPlants(): bool
    {
        return count($this->plants) > 0;
    }

    public function hasMultiplePages(): bool
    {
        return $this->total > $this->perPage;
    }
}
