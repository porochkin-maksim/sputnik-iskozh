<?php declare(strict_types=1);

namespace Core\Repositories;

use Core\Shared\Collections\Collection;
use ReturnTypeWillChange;

final readonly class RepositoryConfig
{
    public function __construct(
        public string $modelClass,
        public string $table,
        public string $collectionClass,
        public string $searchResponseClass,
        public string $searcherClass,
    )
    {
    }

    protected function newInstance(string $class): object
    {
        return new $class();
    }

    #[ReturnTypeWillChange]
    public function makeCollection(): Collection
    {
        return $this->newInstance($this->collectionClass);
    }

    #[ReturnTypeWillChange]
    public function makeSearchResponse(): BaseSearchResponse
    {
        return $this->newInstance($this->searchResponseClass);
    }

    #[ReturnTypeWillChange]
    public function makeSearcher(): SearcherInterface
    {
        return $this->newInstance($this->searcherClass);
    }
}
