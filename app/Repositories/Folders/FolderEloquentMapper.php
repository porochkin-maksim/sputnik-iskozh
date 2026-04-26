<?php declare(strict_types=1);

namespace App\Repositories\Folders;

use App\Models\File\FolderModel;
use Core\Shared\Collections\Collection;
use Core\Domains\Folders\FolderCollection;
use Core\Domains\Folders\FolderEntity;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use IteratorAggregate;

class FolderEloquentMapper implements RepositoryDataMapperInterface
{
    /**
     * @param FolderEntity     $entity
     * @param FolderModel|null $data
     *
     * @return FolderModel
     */
    public function makeRepositoryDataFromEntity($entity, $data = null): object
    {
        if ( ! $data) {
            $result = FolderModel::make();
        }
        else {
            $result = $data;
        }

        if ($entity->getUid()) {
            $result->fill([
                FolderModel::UID => $entity->getUid(),
            ]);
        }

        return $result->fill([
            FolderModel::PARENT_ID => $entity->getParentId(),
            FolderModel::NAME      => $entity->getName(),
        ]);
    }


    /**
     * @param FolderModel $data
     *
     * @return FolderEntity
     */
    public function makeEntityFromRepositoryData($data): object
    {
        return new FolderEntity()
            ->setId($data->{$data::ID})
            ->setName($data->{$data::NAME})
            ->setUid($data->{$data::UID})
            ->setParentId($data->{$data::PARENT_ID})
            ->setCreatedAt($data->{$data::CREATED_AT})
            ->setUpdatedAt($data->{$data::UPDATED_AT})
        ;
    }

    /**
     * @param FolderModel[]|IteratorAggregate $datas
     *
     * @return FolderCollection
     */
    public function makeEntityFromRepositoryDatas(IteratorAggregate|array $datas): Collection
    {
        $result = new FolderCollection();
        foreach ($datas as $data) {
            $result->add($this->makeEntityFromRepositoryData($data));
        }

        return $result;
    }
}
