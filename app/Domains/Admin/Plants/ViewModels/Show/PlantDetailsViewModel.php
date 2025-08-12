<?php

namespace App\Domains\Admin\Plants\ViewModels\Show;

use App\Domains\Admin\Plants\ValueObjects\PlantDetailItem;

readonly class PlantDetailsViewModel
{
    public function __construct(
        public ?string $category = null,
        public ?string $latinName = null,
        public ?string $description = null,
        private ?int   $plantId = null,
    )
    {
    }

    public static function from(?string $category, ?string $latinName, ?string $description, ?int $plantId = null): self
    {
        return new self($category, $latinName, $description, $plantId);
    }

    public function getCategory(): PlantDetailItem
    {
        return PlantDetailItem::create(
            value: $this->category ?? 'N/A',
            isMissing: $this->category === null,
            label: 'Kategorie:',
            contributionName: 'category',
            contributionLabel: 'Kategorie',
            contributionPlaceholder: 'z.B. Kräuter, Stauden',
            contributionType: 'text',
            plantId: $this->plantId
        );
    }

    public function getLatinName(): PlantDetailItem
    {
        return PlantDetailItem::create(
            value: $this->latinName ?? 'N/A',
            isMissing: $this->latinName === null,
            label: 'Botanischer Name:',
            contributionName: 'latin_name',
            contributionLabel: 'Botanischer Name',
            contributionPlaceholder: 'z.B. Solanum lycopersicum',
            contributionType: 'text',
            plantId: $this->plantId
        );
    }

    public function getDescription(): PlantDetailItem
    {
        return PlantDetailItem::create(
            value: $this->description ?? 'N/A',
            isMissing: $this->description === null,
            label: 'Beschreibung:',
            contributionName: 'description',
            contributionLabel: 'Beschreibung',
            contributionPlaceholder: 'z.B. Diese Pflanze ist...',
            contributionType: 'textarea',
            plantId: $this->plantId
        );
    }

    /**
     * @return array<string, PlantDetailItem>
     */
    public function toArray(): array
    {
        return [
            'category' => $this->getCategory(),
            'latin_name' => $this->getLatinName(),
            'description' => $this->getDescription(),
        ];
    }
}
