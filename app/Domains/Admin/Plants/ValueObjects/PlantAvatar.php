<?php

namespace App\Domains\Admin\Plants\ValueObjects;

readonly class PlantAvatar
{
    public function __construct(
        public ?string $src,
        public string  $alt,
        public string  $initials,
        public string  $size,
    )
    {
    }

    public static function create(?string $src, string $alt, string $initials, string $size): self
    {
        return new self($src, $alt, $initials, $size);
    }

    public function hasImage(): bool
    {
        return $this->src !== null;
    }
}
