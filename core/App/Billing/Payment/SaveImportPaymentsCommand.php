<?php declare(strict_types=1);

namespace Core\App\Billing\Payment;

use Core\App\Billing\Invoice\RecalcClaimsPaidCommand;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Payment\PaymentFactory;
use Core\Domains\Billing\Payment\PaymentTransactionService;

readonly class SaveImportPaymentsCommand
{
    public function __construct(
        private PaymentFactory            $paymentFactory,
        private PaymentTransactionService $paymentTransactionService,
        private InvoiceService            $invoiceService,
        private RecalcClaimsPaidCommand   $recalcClaimsPaidCommand,
    )
    {
    }

    public function execute(SaveImportPaymentsInput $input): void
    {
        $recalcInvoiceIds = [];
        $processedAccount = [];

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
            $invoice = $this->invoiceService->getById($invoiceId);
            if ($invoice && ! isset($processedAccount[$invoice->getAccountId()])) {
                $processedAccount[$invoice->getAccountId()] = true;
            }

            $this->recalcClaimsPaidCommand->execute($invoiceId);
        }

        foreach ($processedAccount as $accountId => $_) {
            $this->recalcClaimsPaidCommand->executeForAccount((int) $accountId);
        }
    }
}
