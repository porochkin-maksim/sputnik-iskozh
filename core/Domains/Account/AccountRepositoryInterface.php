<?php declare(strict_types=1);

namespace Core\Domains\Account;

use Core\Repositories\SearcherInterface;

interface AccountRepositoryInterface
{
    public function search(SearcherInterface $searcher): AccountSearchResponse;
    public function getById(?int $id): ?AccountEntity;
    public function getByIds(array $ids): AccountCollection;
    public function save(AccountEntity $entity): AccountEntity;
    public function getByUserId(int $id): AccountCollection;
    public function deleteById(?int $id): bool;
}
