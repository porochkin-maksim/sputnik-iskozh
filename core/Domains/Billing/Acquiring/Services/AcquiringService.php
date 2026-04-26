<?php declare(strict_types=1);

namespace Core\Domains\Billing\Acquiring\Services;

use Core\Domains\Billing\Acquiring\AcquiringEntity;
use Core\Domains\Billing\Acquiring\Contracts\AcquiringRepositoryInterface;
use Core\Domains\Billing\Acquiring\Models\AcquiringSearcher;
use Core\Domains\Billing\Acquiring\Models\AcquiringSearchResponse;

readonly class AcquiringService
{
    public function __construct(
        private AcquiringRepositoryInterface $acquiringRepository,
        private ProviderSelector $providerSelector,
    )
    {
    }

    public function isAvailable(): bool
    {
        return $this->providerSelector->random() !== null;
    }

    public function save(AcquiringEntity $acquiring): AcquiringEntity
    {
        return $this->acquiringRepository->save($acquiring);
    }

    public function getById(int $id): ?AcquiringEntity
    {
        return $this->acquiringRepository->getById($id);
    }

    public function search(AcquiringSearcher $searcher): AcquiringSearchResponse
    {
        return $this->acquiringRepository->search($searcher);
    }

    public function findForInvoiceUserAndAmount(int $invoiceId, int $userId, float $amount): ?AcquiringEntity
    {
        foreach (
            $this->search(
                (new AcquiringSearcher())
                    ->setInvoiceId($invoiceId)
                    ->setUserId($userId)
            )->getItems() as $acquiring
        ) {
            if ($acquiring->getAmount() === $amount && ! $acquiring->getStatus()?->isPaid()) {
                return $acquiring;
            }
        }

        return null;
    }
}
