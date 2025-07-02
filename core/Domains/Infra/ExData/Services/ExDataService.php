<?php declare(strict_types=1);

namespace Core\Domains\Infra\ExData\Services;

use Core\Domains\Infra\ExData\Enums\ExDataTypeEnum;
use Core\Domains\Infra\ExData\Factories\ExDataFactory;
use Core\Domains\Infra\ExData\Models\ExDataDTO;
use Core\Domains\Infra\ExData\Models\ExDataSearcher;
use Core\Domains\Infra\ExData\Repositories\ExDataRepository;

readonly class ExDataService
{
    public function __construct(
        private ExDataFactory    $factory,
        private ExDataRepository $repository,
    )
    {
    }

    public function save(ExDataDTO $dto): ExDataDTO
    {
        $model = $this->repository->getById($dto->getId());
        $model = $this->repository->save($this->factory->makeModelFromDto($dto, $model));

        return $this->factory->makeDtoFromObject($model);
    }

    public function getByTypeAndReferenceId(ExDataTypeEnum $type, int $referenceId): ExDataDTO
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

        return $this->factory->makeDtoFromObject($model);
    }

    public function delete(ExDataDTO $dto): void
    {
        $this->repository->deleteById($dto->getId());
    }

    public function makeDefault(ExDataTypeEnum $type): ExDataDTO
    {
        return $this->factory->makeDefault($type);
    }
} 