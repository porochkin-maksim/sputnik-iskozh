<?php declare(strict_types=1);

namespace Core\Domains\Billing\ClaimToObject;

use Core\Repositories\SearcherInterface;

interface ClaimToObjectRepositoryInterface
{
    public function search(SearcherInterface $searcher): ClaimToObjectSearchResponse;

    public function save(ClaimToObjectEntity $claimToObject): ClaimToObjectEntity;

    public function getById(?int $id): ?ClaimToObjectEntity;

    public function getByIds(array $ids): ClaimToObjectSearchResponse;

    public function deleteById(?int $id): bool;

    public function deleteByClaimId(int $claimId): int;
}
