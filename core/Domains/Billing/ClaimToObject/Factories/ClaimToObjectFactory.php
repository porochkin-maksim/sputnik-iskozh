<?php declare(strict_types=1);

namespace Core\Domains\Billing\ClaimToObject\Factories;

use App\Models\Billing\ClaimToObject;
use Core\Domains\Billing\ClaimToObject\Enums\ClaimObjectTypeEnum;
use Core\Domains\Billing\ClaimToObject\Models\ClaimToObjectDTO;

class ClaimToObjectFactory
{
    public function makeDefault(): ClaimToObjectDTO
    {
        return new ClaimToObjectDTO();
    }

    public function makeModelFromDto(ClaimToObjectDTO $dto, ?ClaimToObject $model = null): ClaimToObject
    {
        if ($model) {
            $result = $model;
        }
        else {
            $result = ClaimToObject::make();
        }

        return $result->fill([
            ClaimToObject::CLAIM_ID => $dto->getClaimId(),
            ClaimToObject::REFERENCE_ID   => $dto->getReferenceId(),
            ClaimToObject::TYPE           => $dto->getType()->value,
        ]);
    }

    public function makeDtoFromObject(ClaimToObject $model): ClaimToObjectDTO
    {
        $result = new ClaimToObjectDTO();

        $result
            ->setClaimId($model->claim_id)
            ->setReferenceId($model->reference_id)
            ->setType(ClaimObjectTypeEnum::tryFrom($model->type))
            ->setCreatedAt($model->created_at)
            ->setUpdatedAt($model->updated_at)
        ;

        return $result;
    }
}