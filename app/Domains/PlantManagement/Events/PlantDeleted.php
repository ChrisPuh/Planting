<?php

namespace App\Domains\PlantManagement\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class PlantDeleted extends ShouldBeStored
{
    public function __construct(
        public readonly string  $plantId,
        public readonly string  $deletedBy,
        public readonly string  $deletedAt,
        public readonly ?string $reason = null,
    )
    {
    }
}
