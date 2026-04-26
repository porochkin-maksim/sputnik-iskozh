<?php declare(strict_types=1);

namespace Core\Domains\Billing\ClaimToObject;

use Core\Domains\Billing\Claim\ClaimEntity;

readonly class ClaimToObjectService
{
    public function __construct(
        private ClaimToObjectRepositoryInterface $claimToObjectRepository,
        private ClaimToObjectFactory $claimToObjectFactory,
    )
    {
    }

    public function search(ClaimToObjectSearcher $searcher): ClaimToObjectSearchResponse
    {
        return $this->claimToObjectRepository->search($searcher);
    }

    public function getById(?int $id): ?ClaimToObjectEntity
    {
        return $this->claimToObjectRepository->getById($id);
    }

    public function save(ClaimToObjectEntity $claimToObject): ClaimToObjectEntity
    {
        return $this->claimToObjectRepository->save($claimToObject);
    }

    public function deleteById(?int $id): bool
    {
        return $this->claimToObjectRepository->deleteById($id);
    }

    public function create(ClaimEntity $claim, ?int $referenceId, ClaimObjectTypeEnum $type): ClaimToObjectEntity
    {
        $claimToObject = $this->claimToObjectFactory->makeDefault()
            ->setClaimId($claim->getId())
            ->setReferenceId($referenceId)
            ->setType($type);

        return $this->save($claimToObject);
    }

    public function getByReference(ClaimObjectTypeEnum $type, int $referenceId): ?ClaimEntity
    {
        return $this->search(
            (new ClaimToObjectSearcher())
                ->setType($type)
                ->setReferenceId($referenceId)
                ->setWithClaim()
                ->setLimit(1),
        )->getItems()->first()?->getClaim();
    }

    public function hasRelations(ClaimEntity $claim): bool
    {
        return ! $this->search(
            (new ClaimToObjectSearcher())
                ->setClaimId($claim->getId())
                ->setLimit(1),
        )->getItems()->isEmpty();
    }

    public function drop(int $claimId): void
    {
        $this->claimToObjectRepository->deleteByClaimId($claimId);
    }
}
