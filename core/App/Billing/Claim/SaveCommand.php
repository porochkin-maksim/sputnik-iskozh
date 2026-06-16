<?php declare(strict_types=1);

namespace Core\App\Billing\Claim;

use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\Billing\Claim\ClaimFactory;
use Core\Domains\Billing\Claim\ClaimService;
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
        ?int $id,
        ?int $invoiceId,
        ?int $serviceId,
        ?float $tariff,
        ?float $cost,
        ?string $name,
        ?float $quantity = null,
    ): ?ClaimEntity
    {
        $this->validator->validate($invoiceId, $serviceId, $tariff, $cost, $name, $quantity);

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
            ->setTariff($tariff ?: $cost)
            ->setQuantity($quantity);

        $claim->setCost((float) $claim->getTariff() * (float) $claim->getQuantity());

        $saved = $this->claimService->save($claim);

        if ($saved->getInvoiceId()) {
            $this->invoiceService->recalcInvoice($saved->getInvoiceId(), true);
        }

        return $saved;
    }
}
