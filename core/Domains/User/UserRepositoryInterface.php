<?php declare(strict_types=1);

namespace Core\Domains\User;

use Core\Repositories\SearcherInterface;

interface UserRepositoryInterface
{
    public function search(SearcherInterface $searcher): UserSearchResponse;

    public function save(UserEntity $user): UserEntity;

    public function getById(?int $id, bool $withDeleted = false): ?UserEntity;

    public function getByIds(array $ids): UserSearchResponse;

    public function deleteById(?int $id): bool;
}
