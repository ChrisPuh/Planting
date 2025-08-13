<?php

namespace App\Domains\PlantManagement\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class PlantCreated extends ShouldBeStored
{
    public function __construct(
        public readonly string  $plantId,
        public readonly string  $name,
        public readonly string  $type,
        public readonly ?string $category,
        public readonly ?string $latinName,
        public readonly ?string $description,
        public readonly ?string $imageUrl,
        public readonly string  $createdBy,
        public readonly string  $createdAt,
        public readonly bool    $wasUserRequested = false,
    )
    {
    }
}
