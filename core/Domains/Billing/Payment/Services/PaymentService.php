<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment\Services;

use Core\Domains\Billing\Payment\Collections\PaymentCollection;
use Core\Domains\Billing\Payment\Events\PaymentsUpdatedEvent;
use Core\Domains\Billing\Payment\Factories\PaymentFactory;
use Core\Domains\Billing\Payment\Models\PaymentComparator;
use Core\Domains\Billing\Payment\Models\PaymentDTO;
use Core\Domains\Billing\Payment\Models\PaymentSearcher;
use Core\Domains\Billing\Payment\Repositories\PaymentRepository;
use Core\Domains\Billing\Payment\Responses\SearchResponse;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\Services\HistoryChangesService;

readonly class PaymentService
{
    public function __construct(
        private PaymentFactory        $paymentFactory,
        private PaymentRepository     $paymentRepository,
        private HistoryChangesService $historyChangesService,
    )
    {
    }

    public function save(PaymentDTO $payment): PaymentDTO
    {
        $model = $this->paymentRepository->getById($payment->getId());
        if ($model) {
            $before = $this->paymentFactory->makeDtoFromObject($model);
        }
        else {
            $before = new PaymentDTO();
        }

        $model   = $this->paymentRepository->save($this->paymentFactory->makeModelFromDto($payment, $model));
        $current = $this->paymentFactory->makeDtoFromObject($model);

        $this->historyChangesService->writeToHistory(
            $payment->getId() ? Event::UPDATE : Event::CREATE,
            HistoryType::INVOICE,
            $current->getInvoiceId(),
            HistoryType::PAYMENT,
            $current->getId(),
            new PaymentComparator($current),
            new PaymentComparator($before),
        );

        if (
            $current->getInvoiceId()
            && (
                $current->getCost() !== $before->getCost()
                ||
                $current->getInvoiceId() !== $before->getInvoice()
            )
        ) {
            PaymentsUpdatedEvent::dispatch($current->getInvoiceId());
        }

        return $current;
    }

    public function search(PaymentSearcher $searcher): SearchResponse
    {
        $response = $this->paymentRepository->search($searcher);

        $result = new SearchResponse();
        $result->setTotal($response->getTotal());

        $collection = new PaymentCollection();
        foreach ($response->getItems() as $item) {
            $collection->add($this->paymentFactory->makeDtoFromObject($item));
        }

        return $result->setItems($collection);
    }

    public function getById(?int $id): ?PaymentDTO
    {
        if ( ! $id) {
            return null;
        }

        $searcher = new PaymentSearcher();
        $searcher
            ->setId($id)
            ->setWithFiles()
        ;
        $result = $this->paymentRepository->search($searcher)->getItems()->first();

        return $result ? $this->paymentFactory->makeDtoFromObject($result) : null;
    }

    public function deleteById(int $id): bool
    {
        $payment = $this->getById($id);

        if ( ! $payment) {
            return false;
        }

        $this->historyChangesService->writeToHistory(
            Event::DELETE,
            HistoryType::INVOICE,
            $payment->getInvoiceId(),
            HistoryType::TRANSACTION,
            $payment->getId(),
        );

        if ($payment->getInvoiceId()) {
            PaymentsUpdatedEvent::dispatch($payment->getInvoiceId());
        }

        return $this->paymentRepository->deleteById($id);
    }
}
