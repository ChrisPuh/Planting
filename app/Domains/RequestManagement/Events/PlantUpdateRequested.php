<?php

namespace App\Domains\RequestManagement\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class PlantUpdateRequested extends ShouldBeStored
{
    public function __construct(
        public readonly string $requestId,
        public readonly string $plantId,
        public readonly array  $proposedChanges,
        public readonly string $reason,
        public readonly string $requestedBy,
        public readonly string $requestedAt,
    )
    {
    }
}
