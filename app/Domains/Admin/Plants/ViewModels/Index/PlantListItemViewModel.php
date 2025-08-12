<?php

namespace App\Domains\Admin\Plants\ViewModels\Index;

readonly class PlantListItemViewModel
{
    public function __construct(
        public int     $id,
        public string  $name,
        public string  $type,
        public ?string $image_url,
        public string  $status, // 'active', 'deleted', 'requested'
        public ?string $category = null,
        public ?string $created_at = null,
    )
    {
    }

    public static function from(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            type: $data['type'],
            image_url: $data['image_url'] ?? null,
            status: self::determineStatus($data),
            category: $data['category'] ?? null,
            created_at: $data['created_at'] ?? null,
        );
    }

    public function isDeleted(): bool
    {
        return $this->status === 'deleted';
    }

    public function isRequested(): bool
    {
        return $this->status === 'requested';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function getStatusBadge(): array
    {
        return match ($this->status) {
            'deleted' => ['text' => 'Gelöscht', 'color' => 'red', 'variant' => 'solid'],
            'requested' => ['text' => 'Beantragt', 'color' => 'sky', 'variant' => null],
            default => ['text' => 'Aktiv', 'color' => 'emerald', 'variant' => null],
        };
    }

    private static function determineStatus(array $data): string
    {
        if (!empty($data['deleted_at'])) {
            return 'deleted';
        }

        if (!empty($data['requested_by']) && !empty($data['requested_at'])) {
            return 'requested';
        }

        return 'active';
    }
}
