<?php declare(strict_types=1);

namespace Core\Domains\Billing\Period\Models;

use Carbon\Carbon;
use Core\Domains\Common\Traits\TimestampsTrait;
use Core\Helpers\DateTime\DateTimeHelper;

class PeriodDTO
{
    use TimestampsTrait;

    private ?int    $id       = null;
    private ?string $name     = null;
    private ?Carbon $start_at = null;
    private ?Carbon $end_at   = null;

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
        return $this->start_at;
    }

    public function setStartAt(mixed $start_at): static
    {
        $this->start_at = DateTimeHelper::toCarbonOrNull($start_at);

        return $this;
    }

    public function getEndAt(): ?Carbon
    {
        return $this->end_at;
    }

    public function setEndAt(mixed $end_at): static
    {
        $this->end_at = DateTimeHelper::toCarbonOrNull($end_at);

        return $this;
    }

    public function isCurrent(): bool
    {
        return Carbon::now()->between($this->start_at, $this->end_at);
    }
}
