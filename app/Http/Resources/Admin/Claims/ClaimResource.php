<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Claims;

use lc;
use App\Http\Resources\AbstractResource;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Billing\Claim\Models\ClaimDTO;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Responses\ResponsesEnum;

readonly class ClaimResource extends AbstractResource
{
    public function __construct(
        private ClaimDTO $claim,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access = lc::roleDecorator();
        $name   = $this->claim->getName();
        if ( ! $name) {
            $name = $this->claim->getService()?->getName();
        }

        return [
            'id'         => $this->claim->getId(),
            'tariff'     => $this->claim->getTariff(),
            'cost'       => $this->claim->getCost(),
            'payed'      => $this->claim->getPayed(),
            'delta'      => $this->claim->getCost() - $this->claim->getPayed(),
            'serviceId'  => $this->claim->getServiceId(),
            'service'    => $name,
            'name'       => $this->claim->getName(),
            'created'    => $this->formatCreatedAt($this->claim->getCreatedAt()),
            'actions'    => [
                ResponsesEnum::VIEW => $access->can(PermissionEnum::CLAIMS_VIEW),
                ResponsesEnum::EDIT => $access->can(PermissionEnum::CLAIMS_EDIT),
                ResponsesEnum::DROP => $access->can(PermissionEnum::CLAIMS_DROP),
            ],
            'historyUrl' => $this->claim->getId()
                ? HistoryChangesLocator::route(
                    referenceType: HistoryType::CLAIM,
                    referenceId  : $this->claim->getId(),
                ) : null,
        ];
    }
}
