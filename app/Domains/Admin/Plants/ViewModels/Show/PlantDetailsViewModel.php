<?php

namespace App\Domains\Admin\Plants\ViewModels\Show;

use App\Domains\Admin\Plants\ValueObjects\PlantDetailItem;
use App\Domains\Admin\Plants\ViewModels\Show\Concerns\HasSectionInfo;

class PlantDetailsViewModel
{
    use HasSectionInfo;

    public function __construct(
        public readonly ?string  $category = null,
        public readonly ?string  $latinName = null,
        public readonly ?string  $description = null,
        private readonly ?string $plantId = null, // ← Flexibel für UUID oder ID
    )
    {
        $this->sectionTitle = 'Details';
        $this->sectionPartial = 'partials.plants.show.details';
        $this->variableName = 'details';
    }

    public static function from(?string $category, ?string $latinName, ?string $description, string|int|null $plantId = null): self
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

    public function isExpandable(): bool
    {
        return true; // Details können eingeklappt werden
    }

    public function getDefaultExpanded(): bool
    {
        return true; // This view model does not support expansion
    }
}
