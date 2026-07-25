<?php declare(strict_types=1);

namespace Core\App\Billing\Payment;

use Core\App\Billing\Payment\Validator\LinkPaymentValidator;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Payment\PaymentEntity;
use Core\Domains\Billing\Payment\PaymentFactory;
use Core\Domains\Billing\Payment\PaymentService;
use Core\Domains\Billing\Payment\PaymentTransactionService;
use Core\Exceptions\ValidationException;

readonly class LinkPaymentCommand
{
    public function __construct(
        private PaymentService            $paymentService,
        private PaymentFactory            $paymentFactory,
        private PaymentTransactionService $paymentTransactionService,
        private InvoiceService            $invoiceService,
        private LinkPaymentValidator      $validator,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(
        ?int    $id,
        ?string $name,
        ?float  $cost,
        ?string $comment,
        ?int    $accountId,
    ): ?PaymentEntity
    {
        $this->validator->validate($cost, $accountId);

        $oldCost = $id ? $this->paymentService->getById($id)?->getCost() : null;

        $payment = $id ? $this->paymentService->getById($id) : $this->paymentFactory->makeDefault();
        if ($payment === null) {
            return null;
        }

        $payment
            ->setVerified(true)
            ->setModerated(true)
            ->setName($name)
            ->setCost($cost)
            ->setComment($comment)
            ->setAccountId($accountId)
            ->setInvoiceId(null)
        ;

        $payment = $this->paymentTransactionService->saveWithTransaction($payment);

        if ($id === null && $payment->getInvoiceId()) {
            $this->invoiceService->recalcInvoice($payment->getInvoiceId(), true, false);
        }
        elseif ($id !== null && $oldCost !== null && $oldCost !== $cost && $payment->getInvoiceId()) {
            $this->invoiceService->recalcInvoice($payment->getInvoiceId(), true, false);
        }

        return $payment;
    }
}
