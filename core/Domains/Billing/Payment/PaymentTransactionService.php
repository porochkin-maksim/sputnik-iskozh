<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment;

use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Transaction\TransactionFactory;
use Core\Domains\Billing\Transaction\TransactionSearcher;
use Core\Domains\Billing\Transaction\TransactionService;

readonly class PaymentTransactionService
{
    public function __construct(
        private PaymentService     $paymentService,
        private TransactionService $transactionService,
        private TransactionFactory $transactionFactory,
        private InvoiceService     $invoiceService,
    )
    {
    }

    public function saveWithTransaction(PaymentEntity $payment): PaymentEntity
    {
        $payment = $this->paymentService->save($payment);

        $searcher = new TransactionSearcher();
        $searcher->setPaymentId($payment->getId())->setClaimId(null);
        $existingTransactions = $this->transactionService->search($searcher)->getItems();

        if ($existingTransactions->count() > 0) {
            $transaction = $existingTransactions->first();
            $transaction->setCost($payment->getCost());
        }
        else {
            $transaction = $this->transactionFactory->makeDefault()
                ->setPaymentId($payment->getId())
                ->setClaimId(null)
                ->setCost($payment->getCost());
        }

        $this->transactionService->save($transaction);

        return $payment;
    }

    public function hasAllocatedTransactions(int $paymentId): bool
    {
        return $this->transactionService->getAllocatedByPaymentId($paymentId)->count() > 0;
    }

    public function delete(int $paymentId): bool
    {
        $invoiceId = $this->paymentService->getById($paymentId)?->getInvoiceId();

        $result = $this->paymentService->deleteById($paymentId);

        if ($result && $invoiceId) {
            $this->invoiceService->recalcInvoice($invoiceId, sync: true);
        }

        return $result;
    }
}
