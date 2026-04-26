<?php declare(strict_types=1);

namespace Core\App\Billing\Service;

use App\Models\Billing\Period;
use App\Models\Billing\Service;
use Core\Domains\Billing\Period\PeriodSearchResponse;
use Core\Domains\Billing\Period\PeriodSearcher;
use Core\Domains\Billing\Period\PeriodService;
use Core\Domains\Billing\Service\ServiceSearchResponse;
use Core\Domains\Billing\Service\ServiceSearcher;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use Core\Repositories\SearcherInterface;

readonly class GetListCommand
{
    public function __construct(
        private ServiceCatalogService $serviceService,
        private PeriodService         $periodService,
    )
    {
    }

    /**
     * @return array{services: ServiceSearchResponse, periods: PeriodSearchResponse}
     */
    public function execute(): array
    {
        return [
            'services' => $this->serviceService->search(
                (new ServiceSearcher())
                    ->excludeType(ServiceTypeEnum::OTHER)
                    ->excludeType(ServiceTypeEnum::DEBT)
                    ->excludeType(ServiceTypeEnum::ADVANCE_PAYMENT)
                    ->withPeriods()
                    ->setSortOrderProperty(Service::PERIOD_ID, SearcherInterface::SORT_ORDER_DESC)
                    ->setSortOrderProperty(Service::ACTIVE, SearcherInterface::SORT_ORDER_DESC)
                    ->setSortOrderProperty(Service::ID, SearcherInterface::SORT_ORDER_ASC),
            ),
            'periods'  => $this->periodService->search(
                (new PeriodSearcher())
                    ->setSortOrderProperty(Period::ID, SearcherInterface::SORT_ORDER_DESC),
            ),
        ];
    }
}
