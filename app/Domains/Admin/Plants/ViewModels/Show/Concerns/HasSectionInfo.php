<?php

namespace App\Domains\Admin\Plants\ViewModels\Show\Concerns;

use Exception;

trait HasSectionInfo
{
    protected ?string $sectionTitle = null;

    protected ?string $sectionPartial = null;

    protected string $variableName;

    public function getTitle(): ?string
    {
        return $this->sectionTitle;
    }

    public function hasTitle(): bool
    {
        return $this->getTitle() !== null;
    }

    /**
     * @throws Exception
     */
    public function getPartial(): string
    {
        return $this->sectionPartial ?? $this->getDefaultPartial();
    }

    /**
     * Override in child class if no $sectionPartial is set
     *
     * @throws Exception
     */
    protected function getDefaultPartial(): string
    {
        // TODO Implement specific Exception or use a more specific message
        throw new Exception('Either set $sectionPartial or override getDefaultPartial() in '.static::class);
    }

    public function getVariableName(): string
    {
        return $this->variableName;
    }
}
