<?php

namespace App\Domains\Admin\Plants\ValueObjects;

readonly class PlantTypeBadge
{
    public function __construct(
        public string $text,
        public string $color,
    ) {}

    public static function create(string $text, string $color): self
    {
        return new self($text, $color);
    }
}
