<?php declare(strict_types=1);

namespace Core\Domains\Billing\Transaction;

use Core\Repositories\SearcherInterface;

interface TransactionRepositoryInterface
{
    public function search(SearcherInterface $searcher): TransactionSearchResponse;

    public function save(TransactionEntity $transaction): TransactionEntity;

    public function getById(?int $id): ?TransactionEntity;

    public function deleteById(?int $id): bool;

    /** @param int[] $claimIds */
    public function deleteByClaimIds(array $claimIds): void;

    /** @param int[] $paymentIds */
    public function deleteByPaymentIds(array $paymentIds): void;
}
