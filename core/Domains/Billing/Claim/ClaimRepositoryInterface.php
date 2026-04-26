<?php declare(strict_types=1);

namespace Core\Domains\Billing\Claim;

use Core\Repositories\SearcherInterface;

interface ClaimRepositoryInterface
{
    public function search(SearcherInterface $searcher): ClaimSearchResponse;

    public function save(ClaimEntity $claim): ClaimEntity;

    public function getById(?int $id): ?ClaimEntity;

    public function getByIds(array $ids): ClaimSearchResponse;

    public function deleteById(?int $id): bool;
}
