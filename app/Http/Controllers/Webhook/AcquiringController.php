<?php declare(strict_types=1);

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Core\Domains\Billing\Acquiring\AcquiringLocator;
use Core\Domains\Billing\Acquiring\Enums\StatusEnum;
use Core\Domains\Billing\Acquiring\Services\AcquiringService;
use Core\Domains\Billing\Invoice\InvoiceLocator;
use Core\Domains\Billing\Invoice\Services\InvoiceService;
use Core\Domains\Billing\Payment\Factories\PaymentFactory;
use Core\Domains\Billing\Payment\PaymentLocator;
use Core\Domains\Billing\Payment\Services\PaymentService;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Domains\Infra\HistoryChanges\Services\HistoryChangesService;
use Core\Services\Money\MoneyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AcquiringController extends Controller
{
    private AcquiringService      $acquiringService;
    private PaymentService        $paymentService;
    private PaymentFactory        $paymentFactory;
    private InvoiceService        $invoiceService;
    private HistoryChangesService $historyChangesService;

    public function __construct()
    {
        $this->acquiringService      = AcquiringLocator::AcquiringService();
        $this->paymentService        = PaymentLocator::PaymentService();
        $this->paymentFactory        = PaymentLocator::PaymentFactory();
        $this->invoiceService        = InvoiceLocator::InvoiceService();
        $this->historyChangesService = HistoryChangesLocator::HistoryChangesService();
    }

    public function submit(int $acquiringId, string $hash): JsonResponse
    {
        $acquiring = $this->acquiringService->getById($acquiringId);

        if ($acquiring && $acquiring->makeHash() === $hash && $acquiring->getStatus()?->isProcess()) {
            DB::beginTransaction();
            $invoice = $this->invoiceService->getById($acquiring->getInvoiceId());
            $payment = $this->paymentFactory->makeDefault()
                ->setName(sprintf('Оплата через "%s"', $acquiring->getProvider()?->name()))
                ->setInvoiceId($acquiring->getInvoiceId())
                ->setCost($acquiring->getAmount())
                ->setModerated(true)
                ->setVerified(true)
                ->setAccountId($invoice?->getAccountId())
                ->setModerated(true)
                ->setVerified(true)
                ->setPaidAt(Carbon::now())
                ->setComment(sprintf('Платёж на сумму %s при проведении оплаты через "%s"',
                    MoneyService::parse($acquiring->getAmount()),
                    $acquiring->getProvider()?->name(),
                ))
            ;

            $payment = $this->paymentService->save($payment);

            $this->addLog(
                $acquiring->getInvoiceId(), sprintf('Подтверждён платёж #%s на сумму %s при проведении оплаты через "%s"',
                $payment->getId(),
                MoneyService::parse($payment->getCost()),
                $acquiring->getProvider()?->name(),
            ),
                $payment->getId(),
            );

            $acquiring
                ->setPaymentId($payment->getId())
                ->setStatus(StatusEnum::PAID)
            ;

            $this->acquiringService->save($acquiring);
            DB::commit();

            return response()->json(true);
        }

        return response()->json(false);
    }

    public function failed(int $acquiringId, string $hash): JsonResponse
    {
        $acquiring = $this->acquiringService->getById($acquiringId);

        if ($acquiring && $acquiring->makeHash() === $hash && $acquiring->getStatus()?->isProcess()) {
            $acquiring->setStatus(StatusEnum::CANCELED);

            $this->acquiringService->save($acquiring);

            return response()->json(true);
        }

        return response()->json(false);
    }

    private function addLog(int $invoiceId, string $text, ?int $paymentId = null): void
    {
        $this->historyChangesService->writeToHistory(
            Event::COMMON,
            HistoryType::INVOICE,
            $invoiceId,
            $paymentId ? HistoryType::PAYMENT : null,
            $paymentId,
            text: $text,
        );
    }
}
