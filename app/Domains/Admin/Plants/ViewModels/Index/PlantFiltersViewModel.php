<?php

namespace App\Domains\Admin\Plants\ViewModels\Index;

readonly class PlantFiltersViewModel
{
    public function __construct(
        public ?string $search = null,
        public ?string $type = null,
        public ?string $status = null,
        public ?string $category = null,
    ) {}

    public static function from(array $filters): self
    {
        return new self(
            search: $filters['search'] ?? null,
            type: $filters['type'] ?? null,
            status: $filters['status'] ?? null,
            category: $filters['category'] ?? null,
        );
    }

    public function hasActiveFilters(): bool
    {
        return ! empty($this->search) ||
            ! empty($this->type) ||
            ! empty($this->status) ||
            ! empty($this->category);
    }

    public function getActiveFiltersCount(): int
    {
        return (int) collect([
            $this->search,
            $this->type,
            $this->status,
            $this->category,
        ])->filter()->count();
    }

    public function toArray(): array
    {
        return array_filter([
            'search' => $this->search,
            'type' => $this->type,
            'status' => $this->status,
            'category' => $this->category,
        ]);
    }
}
