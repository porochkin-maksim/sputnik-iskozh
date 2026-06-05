<?php declare(strict_types=1);

namespace App\Repositories\Shared\DB;

use Core\Contracts\RepositoryDataMapperInterface;
use Core\Repositories\BaseSearchResponse;
use Core\Repositories\RepositoryConfig;
use Core\Repositories\SearcherInterface;
use Core\Shared\Collections\Collection;
use ReturnTypeWillChange;

trait ConfigurableRepositoryTrait
{
    abstract protected function repositoryConfig(): RepositoryConfig;

    abstract protected function getMapper(): ?RepositoryDataMapperInterface;

    protected function modelClass(): string
    {
        return $this->repositoryConfig()->modelClass;
    }

    protected function getTable(): string
    {
        return $this->repositoryConfig()->table;
    }

    protected function getEmptyCollection(): Collection
    {
        return $this->repositoryConfig()->makeCollection();
    }

    #[ReturnTypeWillChange]
    protected function getEmptySearchResponse(): BaseSearchResponse
    {
        return $this->repositoryConfig()->makeSearchResponse();
    }

    #[ReturnTypeWillChange]
    protected function getEmptySearcher(): SearcherInterface
    {
        return $this->repositoryConfig()->makeSearcher();
    }
}
