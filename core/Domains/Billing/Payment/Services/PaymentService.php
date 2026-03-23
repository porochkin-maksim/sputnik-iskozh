<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment\Services;

use Core\Domains\Billing\Payment\Models\PaymentDTO;
use Core\Domains\Billing\Payment\Models\PaymentSearcher;
use Core\Domains\Billing\Payment\Responses\PaymentSearchResponse;
use Core\Domains\Billing\Payment\Repositories\PaymentRepository;

readonly class PaymentService
{
    public function __construct(
        private PaymentRepository $paymentRepository,
    )
    {
    }

    public function search(PaymentSearcher $searcher): PaymentSearchResponse
    {
        return $this->paymentRepository->search($searcher);
    }

    public function getById(?int $id): ?PaymentDTO
    {
        return $this->paymentRepository->getById($id);
    }

    public function save(PaymentDTO $counter): PaymentDTO
    {
        return $this->paymentRepository->save($counter);
    }

    public function deleteById(?int $id): bool
    {
        return $this->paymentRepository->deleteById($id);
    }
}
