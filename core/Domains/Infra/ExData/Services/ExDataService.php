<?php declare(strict_types=1);

namespace Core\Domains\Infra\ExData\Services;

use App\Repositories\Infra\ExDataEloquentMapper;
use Core\Domains\Infra\ExData\ExDataEntity;
use Core\Domains\Infra\ExData\Enums\ExDataTypeEnum;
use Core\Domains\Infra\ExData\Factories\ExDataFactory;
use Core\Domains\Infra\ExData\Models\ExDataSearcher;
use Core\Domains\Infra\ExData\Repositories\ExDataRepository;

readonly class ExDataService
{
    public function __construct(
        private ExDataFactory        $factory,
        private ExDataEloquentMapper $mapper,
        private ExDataRepository     $repository,
    )
    {
    }

    public function save(ExDataEntity $dto): ExDataEntity
    {
        $model = $this->repository->getById($dto->getId());
        $model = $this->repository->save($this->mapper->makeRepositoryDataFromEntity($dto, $model));

        return $this->mapper->makeEntityFromRepositoryData($model);
    }

    public function getByTypeAndReferenceId(ExDataTypeEnum $type, int $referenceId): ExDataEntity
    {
        $searcher = new ExDataSearcher();
        $searcher
            ->setType($type)
            ->setReferenceId($referenceId)
        ;

        $model = $this->repository->search($searcher)->getItems()->first();
        if ( ! $model) {
            return $this->factory->makeByType($type, $referenceId);
        }

        return $this->mapper->makeEntityFromRepositoryData($model);
    }

    public function delete(ExDataEntity $dto): void
    {
        $this->repository->deleteById($dto->getId());
    }

    public function makeDefault(ExDataTypeEnum $type): ExDataEntity
    {
        return $this->factory->makeDefault($type);
    }
}
