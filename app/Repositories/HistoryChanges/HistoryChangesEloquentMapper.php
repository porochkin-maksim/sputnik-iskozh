<?php declare(strict_types=1);

namespace App\Repositories\HistoryChanges;

use App\Models\Infra\HistoryChanges as HistoryChangesModel;
use App\Repositories\Shared\Relations\HistoryChangesUserRelationAssembler;
use Core\Contracts\RepositoryDataMapperInterface;
use Core\Domains\HistoryChanges\HistoryChangesCollection;
use Core\Domains\HistoryChanges\HistoryChangesEntity;
use Core\Domains\HistoryChanges\HistoryChangesFactory;
use Core\Domains\HistoryChanges\HistoryType;
use Core\Domains\HistoryChanges\LogData;
use Core\Shared\Collections\Collection;
use IteratorAggregate;

class HistoryChangesEloquentMapper implements RepositoryDataMapperInterface
{
    public function __construct(
        private readonly HistoryChangesFactory $factory,
        private readonly HistoryChangesUserRelationAssembler $relationAssembler,
    )
    {
    }

    public function makeRepositoryDataFromEntity($entity, $data = null): object
    {
        return ($data ? : HistoryChangesModel::make())->fill([
            HistoryChangesModel::TYPE           => $entity->getType()?->value,
            HistoryChangesModel::REFERENCE_TYPE => $entity->getReferenceType()?->value,
            HistoryChangesModel::USER_ID        => $entity->getUserId(),
            HistoryChangesModel::PRIMARY_ID     => $entity->getPrimaryId(),
            HistoryChangesModel::REFERENCE_ID   => $entity->getReferenceId(),
            HistoryChangesModel::DESCRIPTION    => $entity->getLog()?->toArray() ? : [],
        ]);
    }

    public function makeEntityFromRepositoryData($data): object
    {
        /** @var object $data */
        $result = (new HistoryChangesEntity())
            ->setId($data->{HistoryChangesModel::ID})
            ->setType(HistoryType::tryFrom($data->{HistoryChangesModel::TYPE}))
            ->setReferenceType($data->{HistoryChangesModel::REFERENCE_TYPE} ? HistoryType::tryFrom($data->{HistoryChangesModel::REFERENCE_TYPE}) : null)
            ->setUserId($data->{HistoryChangesModel::USER_ID})
            ->setPrimaryId($data->{HistoryChangesModel::PRIMARY_ID})
            ->setReferenceId($data->{HistoryChangesModel::REFERENCE_ID})
            ->setLog(LogData::fromArray($data->{HistoryChangesModel::DESCRIPTION}))
            ->setCreatedAt($data->{HistoryChangesModel::CREATED_AT})
            ->setUpdatedAt($data->{HistoryChangesModel::UPDATED_AT})
        ;

        $result->setUser(
            $this->relationAssembler->makeUser(
                $data->getRelations()[HistoryChangesModel::USER] ?? null,
                $result->getUserId(),
            ),
        );

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
