<?php declare(strict_types=1);

namespace Core\Domains\Option\Models\DataDTO;

class CounterReadingDay implements DataDTOInterface
{
    private ?int $day = null;

    public function getDay(): ?int
    {
        return $this->day;
    }

    public function setDay(?int $day): static
    {
        $this->day = $day;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'day' => $this->getDay(),
        ];
    }
} 