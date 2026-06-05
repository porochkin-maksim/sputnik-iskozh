<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment;

use Core\Repositories\SearcherInterface;

interface PaymentRepositoryInterface
{
    public function search(SearcherInterface $searcher): PaymentSearchResponse;

    public function save(PaymentEntity $payment): PaymentEntity;

    public function getById(?int $id): ?PaymentEntity;

    public function getByIds(array $ids): PaymentSearchResponse;

    public function deleteById(?int $id): bool;
}
