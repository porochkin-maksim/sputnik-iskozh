<?php declare(strict_types=1);

namespace Core\App\Billing\Claim;

use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\Billing\Claim\ClaimFactory;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Exceptions\ValidationException;

readonly class SaveCommand
{
    public function __construct(
        private ClaimService $claimService,
        private ClaimFactory $claimFactory,
        private SaveValidator $validator,
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
    ): ?ClaimEntity
    {
        $this->validator->validate($invoiceId, $serviceId, $tariff, $cost, $name);

        $claim = $id
            ? $this->claimService->getById($id)
            : $this->claimFactory->makeDefault()
                ->setInvoiceId($invoiceId)
                ->setServiceId($serviceId);

        if ($claim === null) {
            return null;
        }

        $claim
            ->setName($name)
            ->setTariff($tariff ?: $cost)
            ->setCost($cost);

        return $this->claimService->save($claim);
    }
}
