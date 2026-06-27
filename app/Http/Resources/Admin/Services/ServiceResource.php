<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Services;

use App\Http\Resources\AbstractResource;
use App\Support\HistoryChangesRoute;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Billing\Service\ServiceEntity;
use Core\Domains\HistoryChanges\HistoryType;
use lc;

readonly class ServiceResource extends AbstractResource
{
    public function __construct(
        private ServiceEntity $service,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $period  = $this->service->getPeriod();
        $access  = lc::roleDecorator();
        $canEdit = $access->can(PermissionEnum::SERVICES_EDIT) && ( ! $period || ! $period->isClosed());

        return [
            'id'             => $this->service->getId(),
            'type'           => $this->service->getType()?->value,
            'typeName'       => $this->service->getType()?->name(),
            'periodId'       => $this->service->getPeriodId(),
            'periodName'     => $period?->getName(),

            'periodFrom'     => $this->service->getPeriodFrom()?->toDateString(),
            'periodTo'       => $this->service->getPeriodTo()?->toDateString(),
            'name'           => $this->service->getName(),
            'cost'           => $this->service->getCost(),
            'actions'        => [
                'periodClosed' => $period?->isClosed() ?? true,
                'view'         => $access->can(PermissionEnum::SERVICES_VIEW),
                'edit'   => $canEdit,
                'drop'   => $access->can(PermissionEnum::SERVICES_DROP) && ( ! $period || ! $period->isClosed()),
                'period' => $canEdit,
                'type'   => $canEdit,
            ],
            'historyUrl'     => $this->service->getId()
                ? HistoryChangesRoute::make(
                    type     : HistoryType::SERVICE,
                    primaryId: $this->service->getId(),
                ) : null,
        ];
    }
}
