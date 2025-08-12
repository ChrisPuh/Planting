<?php

namespace App\Domains\Admin\Plants\ValueObjects;

readonly class PlantMetadataItem
{
    public function __construct(
        public string  $label,
        public ?string $by,
        public ?string $at,
        public bool    $showBy,
        public string  $colorClass,
    )
    {
    }

    public static function create(
        string  $label,
        ?string $by,
        ?string $at,
        bool    $showBy,
        string  $colorClass
    ): self
    {
        return new self($label, $by, $at, $showBy, $colorClass);
    }

    public function hasBy(): bool
    {
        return $this->showBy && $this->by !== null;
    }

    public function hasAt(): bool
    {
        return $this->at !== null;
    }
}
