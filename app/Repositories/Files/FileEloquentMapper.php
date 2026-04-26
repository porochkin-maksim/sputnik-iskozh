<?php declare(strict_types=1);

namespace App\Repositories\Files;

use App\Models\Files\FileModel;
use Core\Shared\Collections\Collection;
use Core\Domains\Files\FileCollection;
use Core\Domains\Files\FileEntity;
use Core\Domains\Files\FileTypeEnum;
use Core\Domains\Shared\Contracts\RepositoryDataMapperInterface;
use IteratorAggregate;

class FileEloquentMapper implements RepositoryDataMapperInterface
{
    /**
     * @param FileEntity     $entity
     * @param FileModel|null $data
     *
     * @return FileModel
     */
    public function makeRepositoryDataFromEntity($entity, $data = null): object
    {
        if ( ! $data) {
            $result = FileModel::make();
        }
        else {
            $result = $data;
        }

        return $result->fill([
            FileModel::TYPE       => $entity->getType()?->value,
            FileModel::RELATED_ID => $entity->getRelatedId(),
            FileModel::PARENT_ID  => $entity->getParentId(),
            FileModel::ORDER      => $entity->getOrder(),
            FileModel::EXT        => $entity->getExt(),
            FileModel::NAME       => $entity->getName(),
            FileModel::PATH       => $entity->getPath(),
        ]);
    }

    /**
     * @param FileModel $data
     *
     * @return FileEntity
     */
    public function makeEntityFromRepositoryData($data): object
    {
        return new FileEntity()
            ->setId($data->{$data::ID})
            ->setType($data->{$data::TYPE} ? FileTypeEnum::tryFrom($data->{$data::TYPE}) : null)
            ->setRelatedId($data->{$data::RELATED_ID})
            ->setParentId($data->{$data::PARENT_ID})
            ->setOrder($data->{$data::ORDER})
            ->setExt($data->{$data::EXT})
            ->setName($data->{$data::NAME})
            ->setPath($data->{$data::PATH})
            ->setCreatedAt($data->{$data::CREATED_AT})
            ->setUpdatedAt($data->{$data::UPDATED_AT})
        ;
    }

    /**
     * @param FileModel[]|IteratorAggregate $datas
     *
     * @return FileCollection
     */
    public function makeEntityFromRepositoryDatas(IteratorAggregate|array $datas): Collection
    {
        $result = new FileCollection();
        foreach ($datas as $data) {
            $result->add($this->makeEntityFromRepositoryData($data));
        }

        return $result;
    }
}
