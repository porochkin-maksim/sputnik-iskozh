<?php declare(strict_types=1);

namespace Core\App\Billing\Acquiring;

use App\Services\Money\MoneyService;
use Carbon\Carbon;
use Core\Domains\Billing\Acquiring\Enums\StatusEnum;
use Core\Domains\Billing\Acquiring\Services\AcquiringService;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Payment\PaymentFactory;
use Core\Domains\Billing\Payment\PaymentService;
use Core\Domains\HistoryChanges\Event;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Core\Domains\HistoryChanges\HistoryType;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class HandleSubmitWebhookCommand
{
    public function __construct(
        private AcquiringService $acquiringService,
        private PaymentService $paymentService,
        private PaymentFactory $paymentFactory,
        private InvoiceService $invoiceService,
        private HistoryChangesService $historyChangesService,
    )
    {
    }

    public function execute(int $acquiringId, string $hash): bool
    {
        $acquiring = $this->acquiringService->getById($acquiringId);

        if ($acquiring === null || $acquiring->makeHash() !== $hash || ! $acquiring->getStatus()?->isProcess()) {
            return false;
        }

        DB::beginTransaction();

        try {
            $invoice = $this->invoiceService->getById($acquiring->getInvoiceId());
            $payment = $this->paymentFactory->makeDefault()
                ->setName(sprintf('Оплата через "%s"', $acquiring->getProvider()?->name()))
                ->setInvoiceId($acquiring->getInvoiceId())
                ->setCost($acquiring->getAmount())
                ->setModerated(true)
                ->setVerified(true)
                ->setAccountId($invoice?->getAccountId())
                ->setPaidAt(Carbon::now())
                ->setComment(sprintf(
                    'Платёж на сумму %s при проведении оплаты через "%s"',
                    MoneyService::parse($acquiring->getAmount()),
                    $acquiring->getProvider()?->name(),
                ));

            $payment = $this->paymentService->save($payment);

            $this->historyChangesService->writeToHistory(
                Event::COMMON,
                HistoryType::INVOICE,
                $acquiring->getInvoiceId(),
                HistoryType::PAYMENT,
                $payment->getId(),
                text: sprintf(
                    'Подтверждён платёж #%s на сумму %s при проведении оплаты через "%s"',
                    $payment->getId(),
                    MoneyService::parse($payment->getCost()),
                    $acquiring->getProvider()?->name(),
                ),
            );

            $acquiring
                ->setPaymentId($payment->getId())
                ->setStatus(StatusEnum::PAID);

            $this->acquiringService->save($acquiring);
            DB::commit();

            return true;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
