<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Services;

use App\Http\Resources\AbstractResource;
use Core\Domains\Billing\Service\Enums\ServiceTypeEnum;
use Core\Domains\Billing\Service\Models\ServiceDTO;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Resources\RouteNames;

readonly class ServiceResource extends AbstractResource
{
    public function __construct(
        private ServiceDTO $service,
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'id'         => $this->service->getId(),
            'type'       => $this->service->getType()?->value,
            'periodId'   => $this->service->getPeriodId(),
            'name'       => $this->service->getName(),
            'cost'       => $this->service->getCost(), 2,
            'active'     => $this->service->isActive(),
            'actions'    => [
                'type'   => ! $this->service->getId(),
                'active' => $this->service->getType()->value === ServiceTypeEnum::TARGET_FEE->value,
                'drop'   => $this->service->getType()->value === ServiceTypeEnum::TARGET_FEE->value,
            ],
            'historyUrl' => $this->service->getId()
                ? HistoryChangesLocator::route(
                    type: HistoryType::SERVICE,
                    primaryId: $this->service->getId(),
                ) : null,
        ];
    }
}
