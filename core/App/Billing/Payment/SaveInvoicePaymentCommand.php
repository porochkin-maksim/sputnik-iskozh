<?php declare(strict_types=1);

namespace Core\App\Billing\Payment;

use Core\Domains\Billing\Payment\PaymentEntity;
use Core\Domains\Billing\Payment\PaymentFactory;
use Core\Domains\Billing\Payment\PaymentFileService;
use Core\Domains\Billing\Payment\PaymentService;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Exceptions\ValidationException;

readonly class SaveInvoicePaymentCommand
{
    public function __construct(
        private PaymentFactory       $paymentFactory,
        private PaymentService       $paymentService,
        private InvoiceService       $invoiceService,
        private PaymentFileService   $fileService,
        private LinkPaymentValidator $validator,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(
        int     $invoiceId,
        ?int    $paymentId,
        ?float  $cost,
        ?string $name,
        ?string $comment,
        ?string $paidAt,
        array   $files,
    ): ?PaymentEntity
    {
        $invoice = $this->invoiceService->getById($invoiceId);
        if ($invoice === null) {
            return null;
        }

        $this->validator->validate($cost, $invoice->getAccountId());

        $payment = $paymentId
            ? $this->paymentService->getById($paymentId)
            : $this->paymentFactory->makeDefault()
                ->setInvoiceId($invoiceId)
                ->setAccountId($invoice->getAccountId())
        ;

        if ($payment === null) {
            return null;
        }

        $payment
            ->setModerated(true)
            ->setVerified(true)
            ->setCost($cost)
            ->setName($name)
            ->setComment($comment)
            ->setPaidAt($paidAt)
        ;

        $payment = $this->paymentService->save($payment);
        $this->fileService->storeAndSave($files, $payment->getId());

        return $payment;
    }
}
