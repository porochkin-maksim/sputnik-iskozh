<?php declare(strict_types=1);

namespace Core\App\Billing\Payment;

use Core\Domains\Billing\Payment\PaymentFactory;
use Core\Domains\Billing\Payment\PaymentService;

readonly class SaveImportPaymentsCommand
{
    public function __construct(
        private PaymentFactory $paymentFactory,
        private PaymentService $paymentService,
    )
    {
    }

    public function execute(SaveImportPaymentsInput $input): void
    {
        foreach ($input->paymentsData as $paymentData) {
            $invoiceId = $paymentData->invoiceId;
            $cost      = $paymentData->amount;

            if ($invoiceId <= 0 || $cost <= 0) {
                continue;
            }

            $payment = $this->paymentFactory->makeDefault()
                ->setInvoiceId($invoiceId)
                ->setCost($cost)
                ->setVerified(true)
                ->setModerated(true)
                ->setName($paymentData->name ?? 'Импортированный платёж')
            ;

            $this->paymentService->save($payment);
        }
    }
}
