<?php declare(strict_types=1);

namespace Core\Domains\Billing\Claim;

readonly class ClaimService
{
    public function __construct(
        private ClaimRepositoryInterface $claimRepository,
    )
    {
    }

    public function search(ClaimSearcher $searcher): ClaimSearchResponse
    {
        return $this->claimRepository->search($searcher);
    }

    public function getById(?int $id): ?ClaimEntity
    {
        return $this->claimRepository->getById($id);
    }

    public function save(ClaimEntity $item): ClaimEntity
    {
        return $this->claimRepository->save($item);
    }

    public function deleteById(?int $id): bool
    {
        return $this->claimRepository->deleteById($id);
    }

    public function saveCollection(ClaimCollection $claims): ClaimCollection
    {
        $result = new ClaimCollection();
        foreach ($claims as $claim) {
            $result->add($this->save($claim));
        }

        return $result;
    }

    public function getByInvoiceId(?int $invoiceId): ClaimCollection
    {
        $searcher = (new ClaimSearcher())
            ->setInvoiceId($invoiceId)
            ->setWithService();

        return $this->search($searcher)->getItems();
    }
}
