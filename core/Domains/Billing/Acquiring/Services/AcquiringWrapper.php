<?php declare(strict_types=1);

namespace Core\Domains\Billing\Acquiring\Services;

use Core\Domains\Billing\Acquiring\Enums\StatusEnum;
use Core\Domains\Billing\Acquiring\Exceptions\ProviderProcessException;
use Core\Domains\Billing\Acquiring\Exceptions\UndefinedProviderException;
use Core\Domains\Billing\Acquiring\Factories\AcquiringFactory;
use Core\Domains\Billing\Acquiring\Models\AcquiringDTO;
use Core\Domains\Billing\Invoice\Models\InvoiceDTO;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\Services\HistoryChangesService;

class AcquiringWrapper
{
    private AcquiringDTO $acquiring;

    public function __construct(
        private readonly InvoiceDTO            $invoice,
        private readonly float                 $amount,
        private readonly int                   $userId,
        private readonly AcquiringService      $acquiringService,
        private readonly AcquiringFactory      $acquiringFactory,
        private readonly ProviderGateway       $providerGateway,
        private readonly HistoryChangesService $historyChangesService,
    )
    {
        $acquirings = $this->acquiringService->getByInvoiceAndUserId($this->invoice->getId(), $this->userId);

        foreach ($acquirings as $acquiring) {
            if ($acquiring->getAmount() === $this->amount && ! $acquiring->getStatus()?->isPaid()) {
                $this->acquiring = $acquiring;
            }
        }

        if ( ! isset($this->acquiring)) {
            $this->acquiring = $this->acquiringFactory->makeDefault()
                ->setInvoice($this->invoice)
                ->setAmount($this->amount)
                ->setUserId($this->userId)
            ;
        }
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getInvoice(): InvoiceDTO
    {
        return $this->invoice;
    }

    /**
     * @throws UndefinedProviderException
     * @throws ProviderProcessException
     */
    public function getPaymentLink(): string
    {
        try {
            if ( ! $this->acquiring->getId()) {
                $this->acquiring = $this->acquiringService->save($this->acquiring);
            }

            $result = $this->providerGateway->getPaymentLink($this->acquiring);
            $this->acquiring->setStatus(StatusEnum::PROCESS);
            $this->acquiringService->save($this->acquiring);

            return $result;
        }
        catch (ProviderProcessException $e) {
            $this->acquiringService->save($this->acquiring);

            $this->addLog(sprintf('Ошибка получения ссылки на оплату "%s": "%s"', $this->acquiring->getProvider()->name, $e->getMessage()));
            throw $e;
        }
    }

    /**
     * @throws UndefinedProviderException
     * @throws ProviderProcessException
     */
    public function getQrLink(): string
    {
        try {
            if ( ! $this->acquiring->getId()) {
                $this->acquiring = $this->acquiringService->save($this->acquiring);
            }

            $result = $this->providerGateway->getQrLink($this->acquiring);
            $this->acquiring->setStatus(StatusEnum::PROCESS);
            $this->acquiringService->save($this->acquiring);

            return $result;
        }
        catch (ProviderProcessException $e) {
            $this->acquiringService->save($this->acquiring);

            $this->addLog(sprintf('Ошибка получения QR-кода "%s": "%s"', $this->acquiring->getProvider()->name, $e->getMessage()));
            throw $e;
        }
    }

    private function addLog(string $text): void
    {
        $this->historyChangesService->writeToHistory(
            Event::COMMON,
            HistoryType::INVOICE,
            $this->acquiring->getInvoice()->getId(),
            text: $text,
        );
    }
}
