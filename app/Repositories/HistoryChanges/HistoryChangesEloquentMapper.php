<?php declare(strict_types=1);

namespace App\Repositories\HistoryChanges;

use App\Models\Infra\HistoryChanges as HistoryChangesModel;
use App\Repositories\User\UserEloquentMapper;
use Core\Domains\HistoryChanges\HistoryChangesCollection;
use Core\Domains\HistoryChanges\HistoryChangesFactory;
use Core\Domains\HistoryChanges\HistoryChangesEntity;
use Core\Domains\HistoryChanges\HistoryType;
use Core\Domains\HistoryChanges\LogData;
use Core\Domains\User\UserFactory;
use Core\Domains\User\UserIdEnum;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use Core\Shared\Collections\Collection;
use IteratorAggregate;

class HistoryChangesEloquentMapper implements RepositoryDataMapperInterface
{
    public function __construct(
        private readonly HistoryChangesFactory $factory,
    )
    {
    }

    public function makeRepositoryDataFromEntity($entity, $data = null): object
    {
        $result = $data ?: HistoryChangesModel::make();

        return $result->fill([
            HistoryChangesModel::TYPE => $entity->getType()?->value,
            HistoryChangesModel::REFERENCE_TYPE => $entity->getReferenceType()?->value,
            HistoryChangesModel::USER_ID => $entity->getUserId(),
            HistoryChangesModel::PRIMARY_ID => $entity->getPrimaryId(),
            HistoryChangesModel::REFERENCE_ID => $entity->getReferenceId(),
            HistoryChangesModel::DESCRIPTION => $entity->getLog()?->toArray() ?: [],
        ]);
    }

    public function makeEntityFromRepositoryData($data): object
    {
        $result = (new HistoryChangesEntity())
            ->setId($data->id)
            ->setType(HistoryType::tryFrom($data->type))
            ->setReferenceType($data->reference_type ? HistoryType::tryFrom($data->reference_type) : null)
            ->setUserId($data->user_id)
            ->setPrimaryId($data->primary_id)
            ->setReferenceId($data->reference_id)
            ->setLog(LogData::fromArray($data->description))
            ->setCreatedAt($data->created_at)
            ->setUpdatedAt($data->updated_at);

        if (isset($data->getRelations()[HistoryChangesModel::USER])) {
            $result->setUser(app(UserEloquentMapper::class)->makeEntityFromRepositoryData($data->getRelation(HistoryChangesModel::USER)));
        }
        elseif ($result->getUserId() === UserIdEnum::ROBOT) {
            $result->setUser(app(UserFactory::class)->makeRobot());
        }
        else {
            $result->setUser(app(UserFactory::class)->makeUndefined());
        }

        return $result;
    }

    public function makeEntityFromRepositoryDatas(IteratorAggregate|array $datas): Collection
    {
        $result = new HistoryChangesCollection();
        foreach ($datas as $data) {
            $result->add($this->makeEntityFromRepositoryData($data));
        }

        return $result;
    }
}
