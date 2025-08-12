<?php

namespace App\Domains\Admin\Plants\ViewModels\Show;

class PlantActionsViewModel
{
    use Concerns\HasSectionInfo;

    public function __construct(
        public readonly int    $id,
        public readonly string $name,
        public readonly bool   $isDeleted,
    )
    {
        $this->sectionTitle = 'Actions';
        $this->sectionPartial = 'partials.plants.show.actions';
        $this->variableName = 'actions';
    }

    public static function from(int $id, string $name, bool $isDeleted): self
    {
        return new self($id, $name, $isDeleted);
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
                'props' => ['id' => $this->id, 'name' => $this->name]
            ];
        }

        return [
            'type' => 'delete',
            'component' => 'x-plants.buttons.delete',
            'props' => ['id' => $this->id, 'name' => $this->name]
        ];
    }

    public function isExpandable(): bool
    {
        return false; // This view model does not support expansion
    }


}
