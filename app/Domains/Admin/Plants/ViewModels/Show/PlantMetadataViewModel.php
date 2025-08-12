<?php

namespace App\Domains\Admin\Plants\ViewModels\Show;

use App\Domains\Admin\Plants\ValueObjects\PlantMetadataItem;
use Illuminate\Support\Carbon;

readonly class PlantMetadataViewModel
{
    public function __construct(
        public ?string $requestedBy = null,
        public ?string $requestedAt = null,
        public ?string $createdBy = null,
        public ?string $createdAt = null,
        public ?string $updatedBy = null,
        public ?string $updatedAt = null,
        public ?string $deletedBy = null,
        public ?string $deletedAt = null,
        private bool   $isAdmin = false,
    )
    {
    }

    public static function from(
        ?string $requestedBy,
        ?string $requestedAt,
        ?string $createdBy,
        ?string $createdAt,
        ?string $updatedBy,
        ?string $updatedAt,
        ?string $deletedBy,
        ?string $deletedAt,
        bool    $isAdmin = false
    ): self
    {
        return new self(
            $requestedBy,
            $requestedAt,
            $createdBy,
            $createdAt,
            $updatedBy,
            $updatedAt,
            $deletedBy,
            $deletedAt,
            $isAdmin
        );
    }

    public function getCreated(): PlantMetadataItem
    {
        return PlantMetadataItem::create(
            label: 'Erstellt',
            by: $this->createdBy,
            at: $this->formattedCreatedAt(),
            showBy: true,
            colorClass: 'text-zinc-500 dark:text-zinc-400'
        );
    }

    public function getUpdated(): ?PlantMetadataItem
    {
        if (!$this->hasUpdated()) {
            return null;
        }

        return PlantMetadataItem::create(
            label: 'Zuletzt geändert',
            by: $this->updatedBy,
            at: $this->formattedUpdatedAt(),
            showBy: true,
            colorClass: 'text-zinc-500 dark:text-zinc-400'
        );
    }

    public function getRequested(): ?PlantMetadataItem
    {
        if (!$this->wasUserCreateRequest()) {
            return null;
        }

        return PlantMetadataItem::create(
            label: 'Beantragt',
            by: $this->requestedBy,
            at: $this->formattedRequestedAt(),
            showBy: $this->isAdmin,
            colorClass: 'text-zinc-500 dark:text-zinc-400'
        );
    }

    public function getDeleted(): ?PlantMetadataItem
    {
        if (!$this->isDeleted()) {
            return null;
        }

        return PlantMetadataItem::create(
            label: 'Gelöscht',
            by: $this->deletedBy,
            at: $this->formattedDeletedAt(),
            showBy: $this->isAdmin,
            colorClass: 'text-red-500 dark:text-red-400'
        );
    }

    /**
     * @return PlantMetadataItem[]
     */
    public function getVisibleItems(): array
    {
        return array_filter([
            $this->getCreated(),
            $this->getUpdated(),
            $this->getRequested(),
            $this->getDeleted(),
        ]);
    }

    public function wasUserCreateRequest(): bool
    {
        return $this->requestedBy !== null && $this->requestedAt !== null;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function hasUpdated(): bool
    {
        return $this->updatedAt !== null;
    }

    public function hasAnySecondaryInfo(): bool
    {
        return $this->hasUpdated() || $this->wasUserCreateRequest() || $this->isDeleted();
    }

    private function formattedCreatedAt(): ?string
    {
        return $this->createdAt
            ? Carbon::parse($this->createdAt)->format('d.m.Y H:i')
            : null;
    }

    private function formattedUpdatedAt(): ?string
    {
        return $this->updatedAt
            ? Carbon::parse($this->updatedAt)->format('d.m.Y H:i')
            : null;
    }

    private function formattedRequestedAt(): ?string
    {
        return $this->requestedAt
            ? Carbon::parse($this->requestedAt)->format('d.m.Y H:i')
            : null;
    }

    private function formattedDeletedAt(): ?string
    {
        return $this->deletedAt
            ? Carbon::parse($this->deletedAt)->format('d.m.Y H:i')
            : null;
    }
}
