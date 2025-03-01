<?php declare(strict_types=1);

namespace Core\Domains\Infra\HistoryChanges\Factories;

use app;
use App\Models\Infra\HistoryChanges;
use Core\Domains\Infra\Comparator\DTO\ChangesCollection;
use Core\Domains\Infra\HistoryChanges\Enums\Type;
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
            ->setUserId(app::user()->getId())
            ->setPrimaryId(null)
            ->setReferenceId(null)
            ->setLog(null);
    }

    public function makeModelFromDto(HistoryChangesDTO $dto, ?HistoryChanges $historyChanges = null): HistoryChanges
    {
        if ($historyChanges) {
            $result = $historyChanges;
        }
        else {
            $result = HistoryChanges::make();
        }

        return $result->fill([
            HistoryChanges::TYPE         => $dto->getType()?->value,
            HistoryChanges::USER_ID      => $dto->getUserId(),
            HistoryChanges::PRIMARY_ID   => $dto->getPrimaryId(),
            HistoryChanges::REFERENCE_ID => $dto->getReferenceId(),
            HistoryChanges::DESCRIPTION  => $dto->getLog()?->toArray() ?: [],
        ]);
    }

    public function makeDtoFromObject(HistoryChanges $historyChanges): HistoryChangesDTO
    {
        $result = new HistoryChangesDTO();

        $logData = LogData::fromArray($historyChanges->description);
        $result
            ->setId($historyChanges->id)
            ->setType(Type::tryFrom($historyChanges->type))
            ->setUserId($historyChanges->user_id)
            ->setPrimaryId($historyChanges->primary_id)
            ->setReferenceId($historyChanges->reference_id)
            ->setLog($logData)
            ->setCreatedAt($historyChanges->created_at)
            ->setUpdatedAt($historyChanges->updated_at);

        if (isset($historyChanges->getRelations()[HistoryChanges::USER])) {
            $result->setUser($this->userFactory->makeDtoFromObject($historyChanges->getRelation(HistoryChanges::USER)));
        }
        else {
            $result->setUser($this->userFactory->makeUndefined());
        }

        return $result;
    }
}
