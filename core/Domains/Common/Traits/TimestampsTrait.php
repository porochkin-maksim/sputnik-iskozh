<?php declare(strict_types=1);

namespace Core\Domains\Common\Traits;

use Carbon\Carbon;
use Core\Helpers\DateTime\DateTimeHelper;

trait TimestampsTrait
{
    private ?Carbon $createdAt = null;
    private ?Carbon $updatedAt = null;

    public function getCreatedAt(): ?Carbon
    {
        return $this->createdAt;
    }

    public function setCreatedAt(mixed $createdAt): static
    {
        $this->createdAt = DateTimeHelper::toCarbonOrNull($createdAt);

        return $this;
    }

    public function getUpdatedAt(): ?Carbon
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(mixed $updatedAt): static
    {
        $this->updatedAt = DateTimeHelper::toCarbonOrNull($updatedAt);

        return $this;
    }
}
