<?php declare(strict_types=1);

namespace Core\Domains\Billing\Transaction;

use Core\Domains\Billing\Payment\PaymentService;

readonly class TransactionService
{
    public function __construct(
        private TransactionRepositoryInterface $transactionRepository,
        private PaymentService                 $paymentService,
    )
    {
    }

    public function search(TransactionSearcher $searcher): TransactionSearchResponse
    {
        return $this->transactionRepository->search($searcher);
    }

    public function getById(?int $id): ?TransactionEntity
    {
        return $this->transactionRepository->getById($id);
    }

    public function save(TransactionEntity $transaction): TransactionEntity
    {
        return $this->transactionRepository->save($transaction);
    }

    public function deleteById(?int $id): bool
    {
        return $this->transactionRepository->deleteById($id);
    }

    public function deleteByPaymentIds(array $paymentIds): void
    {
        $this->transactionRepository->deleteByPaymentIds($paymentIds);
    }

    public function getByPaymentId(?int $paymentId): TransactionCollection
    {
        return $this->search(new TransactionSearcher()
            ->setPaymentId($paymentId)
            ->setWithClaim(),
        )->getItems();
    }

    public function getAllByPaymentIds(array $paymentIds): TransactionCollection
    {
        return $this->search(
            new TransactionSearcher()
                ->setPaymentIds($paymentIds)
                ->setWithClaim(),
        )->getItems();
    }

    /**
     * @param int[] $paymentIds
     */
    public function getUnallocatedBypaymentIds(array $paymentIds): TransactionCollection
    {
        return $this->search(
            new TransactionSearcher()
                ->setPaymentIds($paymentIds)
                ->setClaimId(null),
        )->getItems();
    }

    public function getUnallocatedBypaymentId(int $paymentId): TransactionCollection
    {
        return $this->getUnallocatedBypaymentIds([$paymentId]);
    }

    /**
     * @param int[] $paymentIds
     */
    public function getUnallocatedByPaymentIdsWithClaim(array $paymentIds): TransactionCollection
    {
        return $this->search(
            new TransactionSearcher()
                ->setPaymentIds($paymentIds)
                ->setClaimId(null)
                ->setWithClaim(),
        )->getItems();
    }

    public function getBalanceByAccountId(int $accountId): float
    {
        $paymentIds = $this->paymentService->getVerifiedByAccount($accountId)->getIds();

        if ($paymentIds === []) {
            return 0.0;
        }

        return $this->getUnallocatedBypaymentIds($paymentIds)->getTotalCost();
    }

    public function getByClaimsIds(array $claimIds): TransactionCollection
    {
        return $this->search(new TransactionSearcher()
                ->setClaimIds($claimIds)
        )->getItems();
    }
}
