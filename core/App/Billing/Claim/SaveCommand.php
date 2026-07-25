<?php declare(strict_types=1);

namespace Core\App\Billing\Claim;

use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\Billing\Claim\ClaimFactory;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\Invoice\InvoiceSearcher;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Exceptions\ValidationException;

readonly class SaveCommand
{
    public function __construct(
        private ClaimService   $claimService,
        private ClaimFactory   $claimFactory,
        private SaveValidator  $validator,
        private InvoiceService $invoiceService,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function execute(
        ?int    $id,
        ?int    $invoiceId,
        ?int    $serviceId,
        ?float  $tariff,
        ?float  $cost,
        ?string $name,
        ?float  $quantity = null,
    ): ?ClaimEntity
    {
        $this->validator->validate($invoiceId, $serviceId, $tariff, $cost, $name, $quantity);

        $this->assertPeriodNotClosed($invoiceId);

        $isNew = $id === null;

        $claim = $isNew
            ? $this->claimFactory->makeDefault()
                ->setInvoiceId($invoiceId)
                ->setServiceId($serviceId)
            : $this->claimService->getById($id);

        if ($claim === null) {
            return null;
        }

        $claim
            ->setName($name)
            ->setTariff($tariff ? : $cost)
            ->setQuantity($quantity)
        ;

        $claim->setCost((float) $claim->getTariff() * (float) $claim->getQuantity());

        $saved = $this->claimService->save($claim);

        if ($saved->getInvoiceId()) {
            $this->invoiceService->recalcInvoice($saved->getInvoiceId(), true, false);
        }

        return $saved;
    }

    /**
     * @throws ValidationException
     */
    private function assertPeriodNotClosed(?int $invoiceId): void
    {
        if ($invoiceId === null) {
            return;
        }

        $invoice = $this->invoiceService->search(
            InvoiceSearcher::make()
                ->setId($invoiceId)
                ->setWithPeriod()
                ->setLimit(1),
        )->getItems()->first();

        if ($invoice === null) {
            return;
        }

        if ($invoice->isLocked()) {
            throw new ValidationException([], 'Период закрыт, редактирование невозможно');
        }
    }
}
