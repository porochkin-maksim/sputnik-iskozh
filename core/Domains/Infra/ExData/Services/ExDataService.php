<?php declare(strict_types=1);

namespace Core\Domains\Infra\ExData\Services;

use Core\Domains\Infra\ExData\Enums\ExDataTypeEnum;
use Core\Domains\Infra\ExData\ExDataEntity;
use Core\Domains\Infra\ExData\Factories\ExDataFactory;
use Core\Domains\Infra\ExData\Models\ExDataSearcher;
use Core\Domains\Infra\ExData\Repositories\ExDataRepositoryInterface;

readonly class ExDataService
{
    public function __construct(
        private ExDataFactory $factory,
        private ExDataRepositoryInterface $repository,
    )
    {
    }

    public function save(ExDataEntity $dto): ExDataEntity
    {
        return $this->repository->save($dto);
    }

    public function getByTypeAndReferenceId(ExDataTypeEnum $type, int $referenceId): ExDataEntity
    {
        $searcher = new ExDataSearcher();
        $searcher
            ->setType($type)
            ->setReferenceId($referenceId)
        ;

        $entity = $this->repository->search($searcher)->getItems()->first();

        return $entity ? : $this->factory->makeByType($type, $referenceId);
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
