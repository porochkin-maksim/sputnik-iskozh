<?php declare(strict_types=1);

namespace Core\Objects\Common\Traits;

use Carbon\Carbon;

trait TimestampsTrait
{
    private ?Carbon $createdAt = null;
    private ?Carbon $updatedAt = null;

    public function getCreatedAt(): ?Carbon
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?Carbon $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?Carbon
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?Carbon $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
