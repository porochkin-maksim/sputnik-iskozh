<?php declare(strict_types=1);

namespace Core\Domains\Billing\Payment;

use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Transaction\TransactionFactory;
use Core\Domains\Billing\Transaction\TransactionSearcher;
use Core\Domains\Billing\Transaction\TransactionService;
use Core\Exceptions\ValidationException;

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
                ->setCost($payment->getCost())
            ;
        }

        $this->transactionService->save($transaction);

        return $payment;
    }

    public function hasAllocatedTransactions(int $paymentId): bool
    {
        return $this->transactionService->getAllocatedByPaymentId($paymentId)->count() > 0;
    }

    /**
     * @throws ValidationException
     */
    public function delete(int $paymentId): bool
    {
        if ($this->hasAllocatedTransactions($paymentId)) {
            throw new ValidationException([], 'Нельзя удалить платёж - по нему есть распределение по услугам');
        }

        $invoiceId = $this->paymentService->getById($paymentId)?->getInvoiceId();

        $result = $this->paymentService->deleteById($paymentId);

        if ($result && $invoiceId) {
            $this->invoiceService->recalcInvoice($invoiceId, true, false);
        }

        return $result;
    }
}
