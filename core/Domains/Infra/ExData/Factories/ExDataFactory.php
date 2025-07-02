<?php declare(strict_types=1);

namespace Core\Domains\Infra\ExData\Factories;

use App\Models\Infra\ExData;
use Core\Domains\Infra\ExData\Enums\ExDataTypeEnum;
use Core\Domains\Infra\ExData\Models\ExDataDTO;

class ExDataFactory
{
    public function makeDefault(ExDataTypeEnum $type): ExDataDTO
    {
        return new ExDataDTO()->setType($type);
    }

    public function makeByType(ExDataTypeEnum $type, int $referenceId): ExDataDTO
    {
        return $this->makeDefault($type)
            ->setReferenceId($referenceId);
    }

    public function makeModelFromDto(ExDataDTO $dto, ?ExData $model = null): ExData
    {
        if ($model) {
            $result = $model;
        } else {
            $result = ExData::make();
        }

        return $result->fill([
            ExData::ID => $dto->getId(),
            ExData::TYPE => $dto->getType()?->value,
            ExData::REFERENCE_ID => $dto->getReferenceId(),
            ExData::DATA => $dto->getData(),
        ]);
    }

    public function makeDtoFromObject(ExData $model): ExDataDTO
    {
        $result = $this->makeDefault(ExDataTypeEnum::tryFrom($model->type));

        $result
            ->setId($model->id)
            ->setReferenceId($model->reference_id)
            ->setData($model->data)
            ->setCreatedAt($model->created_at)
            ->setUpdatedAt($model->updated_at);

        return $result;
    }
} 