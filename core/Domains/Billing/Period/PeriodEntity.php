<?php declare(strict_types=1);

namespace Core\Domains\Billing\Period;

use Carbon\Carbon;
use Core\Domains\Common\Traits\TimestampsTrait;
use Core\Shared\Helpers\DateTime\DateTimeHelper;

class PeriodEntity
{
    use TimestampsTrait;

    private ?int $id = null;
    private ?string $name = null;
    private ?Carbon $startAt = null;
    private ?Carbon $endAt = null;
    private bool $isClosed = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getStartAt(): ?Carbon
    {
        return $this->startAt;
    }

    public function setStartAt(mixed $startAt): static
    {
        $this->startAt = DateTimeHelper::toCarbonOrNull($startAt);

        return $this;
    }

    public function getEndAt(): ?Carbon
    {
        return $this->endAt;
    }

    public function setEndAt(mixed $endAt): static
    {
        $this->endAt = DateTimeHelper::toCarbonOrNull($endAt);

        return $this;
    }

    public function isClosed(): bool
    {
        return $this->isClosed;
    }

    public function setIsClosed(bool $isClosed): static
    {
        $this->isClosed = $isClosed;

        return $this;
    }

    public function isCurrent(): bool
    {
        if ($this->startAt === null || $this->endAt === null) {
            return false;
        }

        return Carbon::now()->between($this->startAt, $this->endAt);
    }
}
