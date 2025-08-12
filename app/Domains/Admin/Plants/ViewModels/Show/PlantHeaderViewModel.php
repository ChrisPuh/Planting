<?php

namespace App\Domains\Admin\Plants\ViewModels\Show;

use App\Domains\Admin\Plants\ValueObjects\PlantAvatar;
use App\Domains\Admin\Plants\ValueObjects\PlantTypeBadge;
use App\Domains\Admin\Plants\ViewModels\Show\Concerns\HasSectionInfo;

class PlantHeaderViewModel
{
    use HasSectionInfo;

    public function __construct(
        public readonly string                 $name,
        public readonly string                 $type,
        public readonly ?string                $imageUrl = null,
        private readonly ?PlantBadgesViewModel $badges = null,
    )
    {
        $this->sectionTitle = null;
        $this->sectionPartial = 'partials.plants.show.heading';
        $this->variableName = 'header';
    }

    public static function from(string $name, string $type, ?string $imageUrl = null, ?PlantBadgesViewModel $badges = null): self
    {
        return new self($name, $type, $imageUrl, $badges);
    }

    public function getAvatar(): PlantAvatar
    {
        return PlantAvatar::create(
            src: $this->imageUrl,
            alt: $this->name,
            initials: substr($this->name, 0, 1),
            size: $this->imageUrl ? 'xl' : 'lg'
        );
    }

    public function getTypeBadge(): PlantTypeBadge
    {
        return PlantTypeBadge::create(
            text: $this->type,
            color: 'lime'
        );
    }

    public function getBadges(): ?PlantBadgesViewModel
    {
        return $this->badges;
    }

    public function hasBadges(): bool
    {
        return $this->badges && $this->badges->hasBadges();
    }
}
