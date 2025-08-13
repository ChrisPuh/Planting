<?php

// App\Domains\Admin\Plants\Contracts\PlantRepositoryInterface.php

namespace App\Domains\Admin\Plants\Contracts;

interface PlantRepositoryInterface
{
    public function findByUuid(string $uuid): ?array;

    public function findWithTimeline(string $uuid): array;

    public function getTimelineEvents(string $plantUuid): array;

    public function getAll(?array $filters = null): array;
}
