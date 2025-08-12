<?php

// App\Domains\Admin\Plants\ValueObjects\TimelineEvent.php
namespace App\Domains\Admin\Plants\ValueObjects;

readonly class TimelineEvent
{
    public function __construct(
        public string  $type,           // 'requested', 'created', 'updated', 'update_requested', 'deleted', 'restored'
        public string  $label,          // "Pflanze beantragt", "Erstellt", etc.
        public ?string $by,
        public ?string $at,
        public bool    $showBy,
        public string  $colorClass,
        public string  $iconName,
        public string  $iconColor,
        public ?array  $details = null, // Zusätzliche Details (z.B. welche Felder geändert)
    )
    {
    }

    public static function requested(?string $by, ?string $at, bool $showBy): self
    {
        return new self(
            type: 'requested',
            label: 'Pflanze beantragt',
            by: $by,
            at: $at,
            showBy: $showBy,
            colorClass: 'text-amber-600 dark:text-amber-400',
            iconName: 'user-plus',
            iconColor: 'text-amber-500'
        );
    }

    public static function created(?string $by, ?string $at, bool $showBy): self
    {
        return new self(
            type: 'created',
            label: 'Erstellt',
            by: $by,
            at: $at,
            showBy: $showBy,
            colorClass: 'text-emerald-600 dark:text-emerald-400',
            iconName: 'plus-circle',
            iconColor: 'text-emerald-500'
        );
    }

    public static function updated(?string $by, ?string $at, bool $showBy, ?array $details = null): self
    {
        return new self(
            type: 'updated',
            label: 'Aktualisiert',
            by: $by,
            at: $at,
            showBy: $showBy,
            colorClass: 'text-blue-600 dark:text-blue-400',
            iconName: 'pencil',
            iconColor: 'text-blue-500',
            details: $details
        );
    }

    public static function updateRequested(?string $by, ?string $at, bool $showBy, ?array $details = null): self
    {
        return new self(
            type: 'update_requested',
            label: 'Änderung beantragt',
            by: $by,
            at: $at,
            showBy: $showBy,
            colorClass: 'text-sky-600 dark:text-sky-400',
            iconName: 'clock',
            iconColor: 'text-sky-500',
            details: $details
        );
    }

    public static function deleted(?string $by, ?string $at, bool $showBy): self
    {
        return new self(
            type: 'deleted',
            label: 'Gelöscht',
            by: $by,
            at: $at,
            showBy: $showBy,
            colorClass: 'text-red-600 dark:text-red-400',
            iconName: 'trash',
            iconColor: 'text-red-500'
        );
    }

    public static function restored(?string $by, ?string $at, bool $showBy): self
    {
        return new self(
            type: 'restored',
            label: 'Wiederhergestellt',
            by: $by,
            at: $at,
            showBy: $showBy,
            colorClass: 'text-green-600 dark:text-green-400',
            iconName: 'arrow-path',
            iconColor: 'text-green-500'
        );
    }

    public function hasBy(): bool
    {
        return $this->showBy && $this->by !== null;
    }

    public function hasAt(): bool
    {
        return $this->at !== null;
    }

    public function hasDetails(): bool
    {
        return $this->details !== null && count($this->details) > 0;
    }
}
