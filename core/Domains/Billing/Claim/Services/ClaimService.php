<?php declare(strict_types=1);

namespace Core\Domains\Billing\Claim\Services;

use Core\Domains\Billing\Claim\Collections\ClaimCollection;
use Core\Domains\Billing\Claim\Models\ClaimDTO;
use Core\Domains\Billing\Claim\Models\ClaimSearcher;
use Core\Domains\Billing\Claim\Repositories\ClaimRepository;
use Core\Domains\Billing\Claim\Responses\ClaimSearchResponse;

readonly class ClaimService
{
    public function __construct(
        private ClaimRepository $claimRepository,
    )
    {
    }

    public function search(ClaimSearcher $searcher): ClaimSearchResponse
    {
        return $this->claimRepository->search($searcher);
    }

    public function getById(?int $id): ?ClaimDTO
    {
        return $this->claimRepository->getById($id);
    }

    public function save(ClaimDTO $item): ClaimDTO
    {
        return $this->claimRepository->save($item);
    }

    public function deleteById(?int $id): bool
    {
        return $this->claimRepository->deleteById($id);
    }

    /**
     * @param ClaimCollection $claims
     */
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
        $sarcher = new ClaimSearcher()
            ->setInvoiceId($invoiceId)
            ->setWithService()
        ;

        return $this->search($sarcher)->getItems();
    }
}
