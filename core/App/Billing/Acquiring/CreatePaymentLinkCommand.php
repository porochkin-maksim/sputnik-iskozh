<?php declare(strict_types=1);

namespace Core\App\Billing\Acquiring;

use Core\Domains\Billing\Acquiring\Enums\StatusEnum;
use Core\Domains\Billing\Acquiring\Exceptions\ProviderProcessException;
use Core\Domains\Billing\Acquiring\Services\AcquiringFactory;
use Core\Domains\Billing\Acquiring\Services\AcquiringService;
use Core\Domains\Billing\Acquiring\Services\ProviderGateway;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\HistoryChanges\Event;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Core\Domains\HistoryChanges\HistoryType;

readonly class CreatePaymentLinkCommand
{
    public function __construct(
        private InvoiceService $invoiceService,
        private AcquiringService $acquiringService,
        private AcquiringFactory $acquiringFactory,
        private ProviderGateway $providerGateway,
        private HistoryChangesService $historyChangesService,
    )
    {
    }

    /**
     * @throws ProviderProcessException
     */
    public function execute(int $invoiceId, float $amount, int $userId): ?string
    {
        $invoice = $this->invoiceService->getById($invoiceId);

        if ($invoice === null || $amount <= 0) {
            return null;
        }

        $acquiring = $this->acquiringService->findForInvoiceUserAndAmount($invoiceId, $userId, $amount)
            ?: $this->acquiringFactory->makeDefault()
                ->setInvoice($invoice)
                ->setAmount($amount)
                ->setUserId($userId);

        try {
            if (! $acquiring->getId()) {
                $acquiring = $this->acquiringService->save($acquiring);
            }

            $link = $this->providerGateway->getPaymentLink($acquiring);
            $acquiring->setStatus(StatusEnum::PROCESS);
            $this->acquiringService->save($acquiring);

            return $link;
        } catch (ProviderProcessException $e) {
            $this->acquiringService->save($acquiring);
            $this->historyChangesService->writeToHistory(
                Event::COMMON,
                HistoryType::INVOICE,
                $acquiring->getInvoice()?->getId(),
                text: sprintf('Ошибка получения ссылки на оплату "%s": "%s"', $acquiring->getProvider()?->name, $e->getMessage()),
            );

            throw $e;
        }
    }
}
