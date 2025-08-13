<?php

namespace App\Domains\RequestManagement\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class RequestRejected extends ShouldBeStored
{
    public function __construct(
        public readonly string $requestId,
        public readonly string $reviewedBy,
        public readonly string $reviewedAt,
        public readonly string $comment,
    )
    {
    }
}
