<?php declare(strict_types=1);

namespace Core\Domains\Billing\ClaimToObject\Models;

use Core\Domains\Billing\ClaimToObject\Enums\ClaimObjectTypeEnum;
use Core\Domains\Common\Traits\TimestampsTrait;

class ClaimToObjectDTO
{
    use TimestampsTrait;

    public ?ClaimObjectTypeEnum $type = null;

    public ?int $claim_id = null;
    public ?int $reference_id   = null;

    public function getType(): ?ClaimObjectTypeEnum
    {
        return $this->type;
    }

    public function setType(?ClaimObjectTypeEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getClaimId(): ?int
    {
        return $this->claim_id;
    }

    public function setClaimId(?int $claim_id): static
    {
        $this->claim_id = $claim_id;

        return $this;
    }

    public function getReferenceId(): ?int
    {
        return $this->reference_id;
    }

    public function setReferenceId(?int $reference_id): static
    {
        $this->reference_id = $reference_id;

        return $this;
    }
}