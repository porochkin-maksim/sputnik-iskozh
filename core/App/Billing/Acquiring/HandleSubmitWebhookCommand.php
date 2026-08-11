<?php declare(strict_types=1);

namespace Core\App\Billing\Acquiring;

use App\Services\Money\MoneyService;
use Carbon\Carbon;
use Core\App\Billing\Invoice\RecalcClaimsPaidCommand;
use Core\Domains\Billing\Acquiring\Enums\StatusEnum;
use Core\Domains\Billing\Acquiring\Services\AcquiringService;
use Core\Domains\Billing\Acquiring\Services\ProviderGateway;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Payment\PaymentFactory;
use Core\Domains\Billing\Payment\PaymentTransactionService;
use Core\Domains\HistoryChanges\Event;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Core\Domains\HistoryChanges\HistoryType;
use Core\Contracts\DbServiceInterface;
use Throwable;

readonly class HandleSubmitWebhookCommand
{
    public function __construct(
        private DbServiceInterface         $dbService,
        private AcquiringService           $acquiringService,
        private ProviderGateway            $providerGateway,
        private PaymentTransactionService  $paymentTransactionService,
        private PaymentFactory             $paymentFactory,
        private InvoiceService             $invoiceService,
        private RecalcClaimsPaidCommand    $recalcClaimsPaidCommand,
        private HistoryChangesService      $historyChangesService,
    )
    {
    }

    public function execute(int $acquiringId, string $hash): bool
    {
        $acquiring = $this->acquiringService->getById($acquiringId);

        if ($acquiring === null || $this->providerGateway->makeHash($acquiring) !== $hash || ! $acquiring->getStatus()?->isProcess()) {
            return false;
        }

        if ( ! $this->providerGateway->isPaid($acquiring)) {
            return false;
        }

        $this->dbService->beginTransaction();

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
                ))
            ;

            $payment = $this->paymentTransactionService->saveWithTransaction($payment);

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
                ->setStatus(StatusEnum::PAID)
            ;

            $this->acquiringService->save($acquiring);
            $this->recalcClaimsPaidCommand->execute($acquiring->getInvoiceId());
            $this->dbService->commit();

            return true;
        }
        catch (Throwable $e) {
            $this->dbService->rollBack();
            throw $e;
        }
    }
}
