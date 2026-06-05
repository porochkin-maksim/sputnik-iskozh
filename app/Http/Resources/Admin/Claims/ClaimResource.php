<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Claims;

use lc;
use App\Http\Resources\AbstractResource;
use App\Support\HistoryChangesRoute;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\HistoryChanges\HistoryType;

readonly class ClaimResource extends AbstractResource
{
    public function __construct(
        private ClaimEntity $claim,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access       = lc::roleDecorator();
        $name         = $this->claim->getName();
        $claimService = $this->claim->getService();
        if ( ! $name) {
            $name = $claimService?->getName();
        }
        $claimServiceType = $claimService?->getType();

        $period = $this->claim->getInvoice()?->getPeriod();

        return [
            'id'         => $this->claim->getId(),
            'tariff'     => $this->claim->getTariff(),
            'cost'       => $this->claim->getCost(),
            'paid'       => $this->claim->getPaid(),
            'delta'      => $this->claim->getCost() - $this->claim->getPaid(),
            'serviceId'  => $this->claim->getServiceId(),
            'isAdvance'  => $claimServiceType?->isAdvance(),
            'isDebt'     => $claimServiceType?->isDebt(),
            'service'    => $name,
            'name'       => $this->claim->getName(),
            'created'    => $this->formatDateTimeForRender($this->claim->getCreatedAt()),
            'actions'    => [
                'view' => $access->can(PermissionEnum::CLAIMS_VIEW),
                'edit' => $access->can(PermissionEnum::CLAIMS_EDIT) && ! $period?->isClosed(),
                'drop' => $access->can(PermissionEnum::CLAIMS_DROP) && ! $period?->isClosed(),
            ],
            'historyUrl' => $this->claim->getId()
                ? HistoryChangesRoute::make(
                    referenceType: HistoryType::CLAIM,
                    referenceId  : $this->claim->getId(),
                ) : null,
        ];
    }
}
