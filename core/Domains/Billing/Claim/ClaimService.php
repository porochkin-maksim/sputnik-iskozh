<?php declare(strict_types=1);

namespace Core\Domains\Billing\Claim;

use App\Models\Billing\Claim;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Repositories\SearcherInterface;

readonly class ClaimService
{
    public function __construct(
        private ClaimRepositoryInterface $claimRepository,
        private InvoiceService           $invoiceService,
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
        $invoiceId = $this->getById($id)?->getInvoiceId();
        $result    = $this->claimRepository->deleteById($id);

        if ($result && $invoiceId) {
            $this->invoiceService->recalcInvoice($invoiceId, true);
        }

        return $result;
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
        return $this->getByInvoiceIds([$invoiceId]);
    }

    /**
     * @param int[] $invoiceIds
     */
    public function getByInvoiceIds(array $invoiceIds): ClaimCollection
    {
        return $this->search(new ClaimSearcher()
            ->setWithService()
            ->addWhere(Claim::INVOICE_ID, SearcherInterface::IN, $invoiceIds),
        )->getItems();
    }
}
