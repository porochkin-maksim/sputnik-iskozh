<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Services;

use lc;
use App\Http\Resources\AbstractResource;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Billing\Service\Enums\ServiceTypeEnum;
use Core\Domains\Billing\Service\Models\ServiceDTO;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Responses\ResponsesEnum;

readonly class ServiceResource extends AbstractResource
{
    public function __construct(
        private ServiceDTO $service,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access  = lc::roleDecorator();
        $period  = $this->service->getPeriod();
        $canEdit = $access->can(PermissionEnum::SERVICES_EDIT) && ! $period?->isClosed();

        return [
            'id'         => $this->service->getId(),
            'type'       => $this->service->getType()?->value,
            'typeName'   => $this->service->getType()?->name(),
            'periodId'   => $this->service->getPeriodId(),
            'periodName' => $period?->getName(),
            'name'       => $this->service->getName(),
            'cost'       => $this->service->getCost(),
            'active'     => $this->service->isActive(),
            'actions'    => [
                'active'            => $canEdit && $this->service->getType()->value === ServiceTypeEnum::TARGET_FEE->value,
                'period'            => $canEdit && ! $this->service->getId(),
                'type'              => $canEdit && ! $this->service->getId(),
                ResponsesEnum::VIEW => $access->can(PermissionEnum::SERVICES_VIEW),
                ResponsesEnum::EDIT => $canEdit,
                ResponsesEnum::DROP => $access->can(PermissionEnum::SERVICES_DROP) && $this->service->getType()?->value === ServiceTypeEnum::TARGET_FEE->value,
            ],
            'historyUrl' => $this->service->getId()
                ? HistoryChangesLocator::route(
                    type     : HistoryType::SERVICE,
                    primaryId: $this->service->getId(),
                ) : null,
        ];
    }
}
