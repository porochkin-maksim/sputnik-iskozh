<?php declare(strict_types=1);

namespace Core\App\Billing\Payment;

use Core\Domains\Billing\Payment\PaymentEntity;
use Core\Domains\Billing\Payment\PaymentService;
use Core\Exceptions\ValidationException;

readonly class LinkPaymentCommand
{
    public function __construct(
        private PaymentService       $paymentService,
        private LinkPaymentValidator $validator,
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
        ?int    $invoiceId,
    ): ?PaymentEntity
    {
        $this->validator->validate($cost, $accountId);

        $payment = $id ? $this->paymentService->getById($id) : null;
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
            ->setInvoiceId($invoiceId)
        ;

        return $this->paymentService->save($payment);
    }
}
