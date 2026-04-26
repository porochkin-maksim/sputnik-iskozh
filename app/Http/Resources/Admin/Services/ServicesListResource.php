<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Services;

use App\Http\Resources\Admin\Periods\PeriodsListResource;
use lc;
use App\Http\Resources\AbstractResource;
use App\Http\Resources\Common\SelectResource;
use App\Support\HistoryChangesRoute;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Billing\Period\PeriodCollection;
use Core\Domains\Billing\Service\ServiceCollection;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use Core\Domains\HistoryChanges\HistoryType;

readonly class ServicesListResource extends AbstractResource
{
    public function __construct(
        private ServiceCollection $serviceCollection,
        private PeriodCollection  $periodCollection,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access         = lc::roleDecorator();
        $types          = array_filter(ServiceTypeEnum::array(), static fn(string $name) => $name !== ServiceTypeEnum::OTHER->name());
        $availableTypes = [];

        $result = [
            'types'      => [
                'all' => new SelectResource($types),
            ],
            'historyUrl' => HistoryChangesRoute::make(
                type: HistoryType::SERVICE,
            ),
            'actions'    => [
                'view' => $access->can(PermissionEnum::SERVICES_VIEW),
                'edit' => $access->can(PermissionEnum::SERVICES_EDIT),
                'drop' => $access->can(PermissionEnum::SERVICES_DROP),
            ],
        ];

        $periods = [];

        foreach ($this->periodCollection as $period) {
            if ($period->isClosed()) {
                continue;
            }
            $periods[$period->getId()]        = $period->getName();
            $availableTypes[$period->getId()] = array_filter($types, static fn(string $type) => match ($type) {
                ServiceTypeEnum::PERSONAL_FEE->name(),
                ServiceTypeEnum::TARGET_FEE->name() => true,
                default                             => false,
            });
        }

        $result['periods']     = new SelectResource($periods);
        $result['periodsInfo'] = new PeriodsListResource($this->periodCollection);

        foreach ($this->serviceCollection as $service) {
            $result['services'][] = new ServiceResource($service);

            $type      = $service->getType();
            $available = match ($type) {
                ServiceTypeEnum::PERSONAL_FEE,
                ServiceTypeEnum::TARGET_FEE => true,
                default                     => false,
            };
            if ( ! $available) {
                unset($availableTypes[$service->getPeriodId()][$type?->value]);
            }
        }

        foreach ($availableTypes as $periodId => $types) {
            $availableTypes[$periodId] = new SelectResource($types);
        }
        $result['types']['available'] = $availableTypes;

        return $result;
    }
}
