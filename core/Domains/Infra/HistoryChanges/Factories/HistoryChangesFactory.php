<?php declare(strict_types=1);

namespace Core\Domains\Infra\HistoryChanges\Factories;

use app;
use App\Models\Infra\HistoryChanges;
use Core\Domains\Infra\Comparator\DTO\ChangesCollection;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\Models\HistoryChangesDTO;
use Core\Domains\Infra\HistoryChanges\Models\LogData;
use Core\Domains\User\Factories\UserFactory;

readonly class HistoryChangesFactory
{
    public function __construct(
        private UserFactory $userFactory,
    )
    {
    }

    public function makeDefault(): HistoryChangesDTO
    {
        return (new HistoryChangesDTO())
            ->setId(null)
            ->setType(null)
            ->setReferenceType(null)
            ->setUserId(app::user()->getId())
            ->setPrimaryId(null)
            ->setReferenceId(null)
            ->setLog(null);
    }

    public function makeModelFromDto(HistoryChangesDTO $dto, ?HistoryChanges $model = null): HistoryChanges
    {
        if ($model) {
            $result = $model;
        }
        else {
            $result = HistoryChanges::make();
        }

        return $result->fill([
            HistoryChanges::TYPE           => $dto->getType()?->value,
            HistoryChanges::REFERENCE_TYPE => $dto->getReferenceType()?->value,
            HistoryChanges::USER_ID        => $dto->getUserId(),
            HistoryChanges::PRIMARY_ID     => $dto->getPrimaryId(),
            HistoryChanges::REFERENCE_ID   => $dto->getReferenceId(),
            HistoryChanges::DESCRIPTION    => $dto->getLog()?->toArray() ? : [],
        ]);
    }

    public function makeDtoFromObject(HistoryChanges $model): HistoryChangesDTO
    {
        $result = new HistoryChangesDTO();

        $logData = LogData::fromArray($model->description);
        $result
            ->setId($model->id)
            ->setType(HistoryType::tryFrom($model->type))
            ->setReferenceType($model->reference_type ?HistoryType::tryFrom($model->reference_type) : null)
            ->setUserId($model->user_id)
            ->setPrimaryId($model->primary_id)
            ->setReferenceId($model->reference_id)
            ->setLog($logData)
            ->setCreatedAt($model->created_at)
            ->setUpdatedAt($model->updated_at);

        if (isset($model->getRelations()[HistoryChanges::USER])) {
            $result->setUser($this->userFactory->makeDtoFromObject($model->getRelation(HistoryChanges::USER)));
        }
        else {
            $result->setUser($this->userFactory->makeUndefined());
        }

        return $result;
    }
}
