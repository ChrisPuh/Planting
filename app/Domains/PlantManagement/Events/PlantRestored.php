<?php

namespace App\Domains\PlantManagement\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class PlantRestored extends ShouldBeStored
{
    public function __construct(
        public readonly string $plantId,
        public readonly string $restoredBy,
        public readonly string $restoredAt,
    )
    {
    }
}
