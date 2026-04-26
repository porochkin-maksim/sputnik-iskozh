<?php declare(strict_types=1);

namespace Core\Domains\Option;

use Core\Repositories\SearcherInterface;

interface OptionRepositoryInterface
{
    public function search(SearcherInterface $searcher): OptionSearchResponse;

    public function save(OptionEntity $entity): OptionEntity;

    public function getById(?int $id): ?OptionEntity;
}
