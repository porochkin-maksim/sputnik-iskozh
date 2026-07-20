<?php declare(strict_types=1);

namespace App\Http\Resources\Profile\Claims;

use App\Http\Resources\AbstractResource;
use Core\Domains\Billing\Claim\ClaimEntity;

readonly class ClaimResource extends AbstractResource
{
    public function __construct(
        private ClaimEntity $claim,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $name = $this->claim->getName();
        if ( ! $name) {
            $name = $this->claim->getService()?->getName();
        }

        return [
            'id'        => $this->claim->getId(),
            'tariff'    => $this->claim->getTariff(),
            'quantity'  => $this->claim->getQuantity(),
            'cost'      => $this->claim->getCost(),
            'paid'      => (float) ($this->claim->getPaid() ?? 0),
            'serviceId' => $this->claim->getServiceId(),
            'service'   => $name,
            'name'      => $this->claim->getName(),
            'created'   => $this->formatDateTimeForRender($this->claim->getCreatedAt()),
        ];
    }
} 
