<?php

namespace App\Domains\Admin\Plants\ValueObjects;
readonly class PlantBadge
{
    public function __construct(
        public string  $text,
        public string  $color,
        public ?string $variant = null,
    )
    {
    }
}
