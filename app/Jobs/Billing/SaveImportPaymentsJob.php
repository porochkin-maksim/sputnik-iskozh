<?php declare(strict_types=1);

namespace App\Jobs\Billing;

use Core\Domains\Billing\Payment\PaymentFactory;
use Core\Domains\Billing\Payment\PaymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SaveImportPaymentsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly array $paymentsData,
    )
    {
    }

    public function handle(
        PaymentFactory $paymentFactory,
        PaymentService $paymentService,
    ): void
    {
        foreach ($this->paymentsData as $paymentData) {
            $invoiceId = (int) ($paymentData['invoice_id'] ?? 0);
            $cost      = (float) ($paymentData['amount'] ?? 0);

            if ( ! $invoiceId || ! $cost || $cost <= 0) {
                continue;
            }

            $payment = $paymentFactory->makeDefault()
                ->setInvoiceId($invoiceId)
                ->setCost($cost)
                ->setVerified(true)
                ->setModerated(true)
                ->setName('Импортированный платёж')
            ;

            $paymentService->save($payment);
        }
    }
}
