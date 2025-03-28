<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Services;

use lc;
use App\Http\Resources\AbstractResource;
use App\Http\Resources\Common\SelectResource;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Billing\Period\Collections\PeriodCollection;
use Core\Domains\Billing\Service\Collections\ServiceCollection;
use Core\Domains\Billing\Service\Enums\ServiceTypeEnum;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Responses\ResponsesEnum;

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
            'historyUrl' => HistoryChangesLocator::route(
                type: HistoryType::SERVICE,
            ),
            'actions'    => [
                ResponsesEnum::VIEW => $access->can(PermissionEnum::SERVICES_VIEW),
                ResponsesEnum::EDIT => $access->can(PermissionEnum::SERVICES_EDIT),
                ResponsesEnum::DROP => $access->can(PermissionEnum::SERVICES_DROP),
            ],
        ];

        $periods = [];

        foreach ($this->periodCollection as $period) {
            $periods[$period->getId()]        = $period->getName();
            $availableTypes[$period->getId()] = $types;
        }

        $result['periods'] = new SelectResource($periods);

        foreach ($this->serviceCollection as $service) {
            $result[ResponsesEnum::SERVICES][] = new ServiceResource($service);

            $type      = $service->getType();
            $available = match ($type) {
                ServiceTypeEnum::MEMBERSHIP_FEE,
                ServiceTypeEnum::ELECTRIC_TARIFF => false,
                default                          => true,
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
