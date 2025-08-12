<?php

namespace App\Domains\Admin\Plants\ViewModels\Show;

class PlantActionsViewModel
{
    use Concerns\HasSectionInfo;

    public function __construct(
        public string        $uuid,
        public readonly string $name,
        public readonly bool $isDeleted,
    )
    {
        $this->sectionTitle = 'Actions';
        $this->sectionPartial = 'partials.plants.show.actions';
        $this->variableName = 'actions';
    }

    public static function from(string $uuid, string $name, bool $isDeleted): self  // ← Updated
    {
        return new self($uuid, $name, $isDeleted);
    }

    public function getBackRoute(): string
    {
        return route('plants.index');
    }

    public function getPrimaryAction(): array
    {
        if ($this->isDeleted) {
            return [
                'type' => 'restore',
                'component' => 'x-plants.buttons.restore',
                'props' => ['uuid' => $this->uuid, 'name' => $this->name]  // ← Changed
            ];
        }

        return [
            'type' => 'delete',
            'component' => 'x-plants.buttons.delete',
            'props' => ['uuid' => $this->uuid, 'name' => $this->name]  // ← Changed
        ];
    }

    public function isExpandable(): bool
    {
        return false; // This view model does not support expansion
    }


}
