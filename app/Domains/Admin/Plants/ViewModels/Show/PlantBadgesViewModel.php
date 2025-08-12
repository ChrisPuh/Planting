<?php

namespace App\Domains\Admin\Plants\ViewModels\Show;

use App\Domains\Admin\Plants\ValueObjects\PlantBadge;

readonly class PlantBadgesViewModel
{
    public function __construct(
        private bool $wasUserCreateRequest,
        private bool $isDeleted,
    ) {}

    public static function from(PlantMetadataViewModel $metadata): self
    {
        return new self(
            $metadata->wasUserCreateRequest(),
            $metadata->isDeleted()
        );
    }

    /**
     * @return PlantBadge[]
     */
    public function all(): array
    {
        $badges = [];

        if ($this->wasUserCreateRequest) {
            $badges[] = new PlantBadge(
                text: __('Created By User'),
                color: 'sky',
                variant: null
            );
        }

        if ($this->isDeleted) {
            $badges[] = new PlantBadge(
                text: 'Gelöscht',
                color: 'red',
                variant: 'solid'
            );
        }

        return $badges;
    }

    public function hasBadges(): bool
    {
        return $this->wasUserCreateRequest || $this->isDeleted;
    }
}
