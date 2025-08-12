<?php

namespace App\Domains\Admin\Plants\ViewModels\Show;

use App\Domains\Admin\Plants\ValueObjects\PlantMetadataItem;
use App\Domains\Admin\Plants\ValueObjects\TimelineEvent;
use App\Domains\Admin\Plants\ViewModels\Show\Concerns\HasSectionInfo;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

class PlantMetadataViewModel
{
    use HasSectionInfo;


    public function __construct(
        public readonly ?string $requestedBy = null,
        public readonly ?string $requestedAt = null,
        public readonly ?string $createdBy = null,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedBy = null,
        public readonly ?string $updatedAt = null,
        public readonly ?string $deletedBy = null,
        public readonly ?string $deletedAt = null,
        private readonly bool   $isAdmin = false,
        private readonly array  $timelineEvents = [], // Externe Timeline-Events


    )
    {
        $this->sectionTitle = __('Metadaten');
        $this->sectionPartial = 'partials.plants.show.metadata';
        $this->variableName = 'metadata';


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
        bool    $isAdmin = false,
        array   $timelineEvents = []
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
            $isAdmin,
            $timelineEvents
        );
    }

    /**
     * @return Collection<TimelineEvent>
     * TODO extract to service or repository
     */
    public function getTimelineEvents(): Collection
    {
        // Einfach die übergebenen Events zurückgeben
        return collect($this->timelineEvents);
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
        // TODO extract to service or value object
        return $this->createdAt
            ? Carbon::parse($this->createdAt)->format('d.m.Y H:i')
            : null;
    }

    private function formattedUpdatedAt(): ?string
    {
        // TODO extract to service or value object
        return $this->updatedAt
            ? Carbon::parse($this->updatedAt)->format('d.m.Y H:i')
            : null;
    }

    private function formattedRequestedAt(): ?string
    {
        // TODO extract to service or value object
        return $this->requestedAt
            ? Carbon::parse($this->requestedAt)->format('d.m.Y H:i')
            : null;
    }

    private function formattedDeletedAt(): ?string
    {
        // TODO extract to service or value object
        return $this->deletedAt
            ? Carbon::parse($this->deletedAt)->format('d.m.Y H:i')
            : null;
    }

    public function isExpandable(): bool
    {
        return true; // Metadaten können eingeklappt werden
    }
    public function getDefaultExpanded(): bool
    {
        return false; // Standardmäßig nicht erweitert
    }
}
