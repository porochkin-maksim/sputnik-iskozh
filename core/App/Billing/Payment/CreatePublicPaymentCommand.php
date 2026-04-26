<?php declare(strict_types=1);

namespace Core\App\Billing\Payment;

use Core\Domains\Account\AccountService;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Payment\PaymentFactory;
use Core\Domains\Billing\Payment\PaymentFileService;
use Core\Domains\Billing\Payment\PaymentService;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class CreatePublicPaymentCommand
{
    public function __construct(
        private PaymentService     $paymentService,
        private PaymentFactory     $paymentFactory,
        private PaymentFileService $fileService,
        private AccountService     $accountService,
        private InvoiceService     $invoiceService,
        private CreatePublicPaymentValidator $validator,
    )
    {
    }

    /**
     * @param UploadedFile[] $files
     *
     * @throws Throwable
     */
    public function execute(
        ?int    $invoiceId,
        ?string $accountNumber,
        float   $cost,
        string  $text,
        string  $fullText,
        array   $files,
    ): void
    {
        $this->validator->validate($text, $cost);

        DB::transaction(function () use (
            $invoiceId,
            $accountNumber,
            $cost,
            $text,
            $fullText,
            $files
        ) {
            $payment = $this->paymentFactory->makeDefault();
            $invoice = $invoiceId ? $this->invoiceService->getById($invoiceId) : null;

            if ($invoice !== null) {
                $payment
                    ->setInvoiceId($invoice->getId())
                    ->setAccountId($invoice->getAccountId())
                    ->setComment($text)
                ;
            }
            elseif ($accountNumber) {
                $account = $this->accountService->findByNumber($accountNumber);
                if ($account !== null) {
                    $payment->setAccountId($account->getId());
                }

                $payment->setComment($fullText);
            }
            else {
                $payment->setComment($fullText);
            }

            $payment->setCost($cost);
            $payment = $this->paymentService->save($payment);

            $this->fileService->storeAndSave($files, $payment->getId());
        });
    }
}
