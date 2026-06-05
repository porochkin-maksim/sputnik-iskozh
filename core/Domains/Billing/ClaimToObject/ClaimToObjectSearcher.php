<?php declare(strict_types=1);

namespace Core\Domains\Billing\ClaimToObject;

use App\Models\Billing\ClaimToObject;
use Core\Repositories\BaseSearcher;
use Core\Repositories\SearcherInterface;

class ClaimToObjectSearcher extends BaseSearcher
{
    public function setClaimId(?int $claimId): static
    {
        $this->addWhere(ClaimToObject::CLAIM_ID, SearcherInterface::EQUALS, $claimId);

        return $this;
    }

    public function setReferenceId(?int $referenceId): static
    {
        $this->addWhere(ClaimToObject::REFERENCE_ID, SearcherInterface::EQUALS, $referenceId);

        return $this;
    }

    public function setType(ClaimObjectTypeEnum $type): static
    {
        $this->addWhere(ClaimToObject::TYPE, SearcherInterface::EQUALS, $type->value);

        return $this;
    }

    public function setWithClaim(): static
    {
        $this->with[] = ClaimToObject::RELATION_CLAIM;

        return $this;
    }
}
