<?php declare(strict_types=1);

namespace Core\Domains\Billing\ClaimToObject\Services;

use App\Models\Billing\ClaimToObject;
use Core\Domains\Billing\Claim\Models\ClaimDTO;
use Core\Domains\Billing\Claim\ClaimLocator;
use Core\Domains\Billing\ClaimToObject\Enums\ClaimObjectTypeEnum;

readonly class ClaimToObjectService
{
    public function create(ClaimDTO $claim, ?int $referenceId, ClaimObjectTypeEnum $type): void
    {
        ClaimToObject::make([
            ClaimToObject::CLAIM_ID     => $claim->getId(),
            ClaimToObject::REFERENCE_ID => $referenceId,
            ClaimToObject::TYPE         => $type->value,
        ])->save();
    }

    public function getByReference(ClaimObjectTypeEnum $type, int $referenceId): ?ClaimDTO
    {
        $claimId = ClaimToObject::where(ClaimToObject::TYPE, $type->value)
            ->where(ClaimToObject::REFERENCE_ID, $referenceId)
            ->value(ClaimToObject::CLAIM_ID)
        ;

        return ClaimLocator::ClaimService()->getById($claimId);
    }

    public function hasRelations(ClaimDTO $claim): bool
    {
        return (bool) ClaimToObject::where(ClaimToObject::CLAIM_ID, $claim->getId())->first();
    }

    public function drop(ClaimDTO $claim): void
    {
        ClaimToObject::where(ClaimToObject::CLAIM_ID, $claim->getId())->delete();
    }
}
