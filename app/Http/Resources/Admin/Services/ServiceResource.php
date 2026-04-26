<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Services;

use lc;
use App\Http\Resources\AbstractResource;
use App\Support\HistoryChangesRoute;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Billing\Service\ServiceEntity;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use Core\Domains\HistoryChanges\HistoryType;

readonly class ServiceResource extends AbstractResource
{
    public function __construct(
        private ServiceEntity $service,
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
                'active'            => $canEdit && in_array($this->service->getType(), [ServiceTypeEnum::TARGET_FEE, ServiceTypeEnum::PERSONAL_FEE], true),
                'period'            => $canEdit && ! $this->service->getId(),
                'type'              => $canEdit && ! $this->service->getId(),
                'view' => $access->can(PermissionEnum::SERVICES_VIEW),
                'edit' => $canEdit,
                'drop' => $access->can(PermissionEnum::SERVICES_DROP) && in_array($this->service->getType(), [ServiceTypeEnum::TARGET_FEE, ServiceTypeEnum::PERSONAL_FEE], true),
            ],
            'historyUrl' => $this->service->getId()
                ? HistoryChangesRoute::make(
                    type     : HistoryType::SERVICE,
                    primaryId: $this->service->getId(),
                ) : null,
        ];
    }
}
