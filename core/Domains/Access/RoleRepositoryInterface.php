<?php declare(strict_types=1);

namespace Core\Domains\Access;

use Core\Repositories\SearcherInterface;

interface RoleRepositoryInterface
{
    public function search(SearcherInterface $searcher): RoleSearchResponse;

    public function save(RoleEntity $role): RoleEntity;

    public function getById(?int $id): ?RoleEntity;

    public function getByIds(array $ids): RoleSearchResponse;

    public function deleteById(?int $id): bool;

    public function getByUserId(int $id): ?RoleEntity;
}
