<?php declare(strict_types=1);

namespace Core\App\Billing\Payment;

use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Payment\PaymentFactory;
use Core\Domains\Billing\Payment\PaymentTransactionService;

readonly class SaveImportPaymentsCommand
{
    public function __construct(
        private PaymentFactory            $paymentFactory,
        private PaymentTransactionService $paymentTransactionService,
        private InvoiceService            $invoiceService,
    )
    {
    }

    public function execute(SaveImportPaymentsInput $input): void
    {
        $recalcInvoiceIds = [];

        foreach ($input->paymentsData as $paymentData) {
            $invoiceId = $paymentData->invoiceId;
            $cost      = $paymentData->amount;

            if ($invoiceId <= 0 || $cost <= 0) {
                continue;
            }

            $payment = $this->paymentFactory->makeDefault()
                ->setInvoiceId($invoiceId)
                ->setAccountId($paymentData->accountId)
                ->setCost($cost)
                ->setVerified(true)
                ->setModerated(true)
                ->setName($paymentData->name ?? 'Импортированный платёж')
            ;

            $this->paymentTransactionService->saveWithTransaction($payment);

            $recalcInvoiceIds[$invoiceId] = $invoiceId;
        }

        foreach ($recalcInvoiceIds as $invoiceId) {
            $this->invoiceService->recalcInvoice($invoiceId, true);
        }
    }
}
