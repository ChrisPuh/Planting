<?php

namespace App\Domains\RequestManagement\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class PlantCreationRequested extends ShouldBeStored
{
    public function __construct(
        public readonly string $requestId,
        public readonly string $plantId,  // Target Plant UUID
        public readonly array  $proposedData,
        public readonly string $reason,
        public readonly string $requestedBy,
        public readonly string $requestedAt,
    )
    {
    }
}
