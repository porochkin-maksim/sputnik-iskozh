<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment;

readonly class PaymentService
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
    )
    {
    }

    public function search(PaymentSearcher $searcher): PaymentSearchResponse
    {
        return $this->paymentRepository->search($searcher);
    }

    public function getById(?int $id): ?PaymentEntity
    {
        return $this->paymentRepository->getById($id);
    }

    public function save(PaymentEntity $payment): PaymentEntity
    {
        return $this->paymentRepository->save($payment);
    }

    public function deleteById(?int $id): bool
    {
        return $this->paymentRepository->deleteById($id);
    }
}
