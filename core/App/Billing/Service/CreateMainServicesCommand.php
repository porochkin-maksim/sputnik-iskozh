<?php declare(strict_types=1);

namespace Core\App\Billing\Service;

use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceFactory;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use Core\Domains\HistoryChanges\Event;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Core\Domains\HistoryChanges\HistoryType;

readonly class CreateMainServicesCommand
{
    public function __construct(
        private ServiceCatalogService $serviceService,
        private ServiceFactory        $serviceFactory,
        private HistoryChangesService $historyChangesService,
    )
    {
    }

    public function execute(int $periodId): void
    {
        $cases = [
            ServiceTypeEnum::MEMBERSHIP_FEE,
            ServiceTypeEnum::ELECTRIC_TARIFF,
            ServiceTypeEnum::OTHER,
            ServiceTypeEnum::DEBT,
            ServiceTypeEnum::ADVANCE_PAYMENT,
        ];

        foreach ($cases as $case) {
            $service = $this->serviceService->getByPeriodIdAndType($periodId, $case);
            if ($service !== null) {
                continue;
            }

            $service = $this->serviceFactory->makeDefault()
                ->setPeriodId($periodId)
                ->setType($case)
                ->setName($case->name())
                ->setCost(0)
            ;

            $service = $this->serviceService->save($service);

            $this->historyChangesService->writeToHistory(
                Event::CREATE,
                HistoryType::PERIOD,
                $periodId,
                HistoryType::SERVICE,
                $service->getId(),
            );
        }
    }
}
