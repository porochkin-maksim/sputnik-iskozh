<?php declare(strict_types=1);

namespace Core\Domains\Infra\Comparator\DTO;

readonly class Changes
{
    public function __construct(
        private string $title,
        private string $oldValue,
        private string $newValue,
    )
    {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getOldValue(): string
    {
        return $this->oldValue;
    }

    public function getNewValue(): string
    {
        return $this->newValue;
    }
}