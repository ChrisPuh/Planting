<?php

namespace App\Domains\Admin\Plants\ValueObjects;

readonly class PlantDetailItem
{
    public function __construct(
        public string $value,
        public bool   $isMissing,
        public string $label,
        public array  $contribution,
    ) {}

    public static function create(
        string $value,
        bool   $isMissing,
        string $label,
        string $contributionName,
        string $contributionLabel,
        string $contributionPlaceholder,
        string $contributionType,
        ?int   $plantId = null,
        bool   $required = false
    ): self {
        return new self(
            value: $value,
            isMissing: $isMissing,
            label: $label,
            contribution: [
                'id' => $plantId,
                'name' => $contributionName,
                'label' => $contributionLabel,
                'placeholder' => $contributionPlaceholder,
                'required' => $required,
                'type' => $contributionType,
            ]
        );
    }
}
