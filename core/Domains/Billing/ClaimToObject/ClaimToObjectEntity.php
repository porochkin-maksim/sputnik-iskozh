<?php declare(strict_types=1);

namespace Core\Domains\Billing\ClaimToObject;

use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\Common\Traits\TimestampsTrait;

class ClaimToObjectEntity
{
    use TimestampsTrait;

    private ?int $id = null;
    private ?ClaimObjectTypeEnum $type = null;
    private ?int $claimId = null;
    private ?int $referenceId = null;
    private ?ClaimEntity $claim = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

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
        return $this->claimId;
    }

    public function setClaimId(?int $claimId): static
    {
        $this->claimId = $claimId;

        return $this;
    }

    public function getReferenceId(): ?int
    {
        return $this->referenceId;
    }

    public function setReferenceId(?int $referenceId): static
    {
        $this->referenceId = $referenceId;

        return $this;
    }

    public function getClaim(): ?ClaimEntity
    {
        return $this->claim;
    }

    public function setClaim(?ClaimEntity $claim): static
    {
        $this->claim = $claim;

        return $this;
    }
}
