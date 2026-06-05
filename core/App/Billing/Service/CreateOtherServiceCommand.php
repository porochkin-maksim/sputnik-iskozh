<?php declare(strict_types=1);

namespace Core\App\Billing\Service;

use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceFactory;
use Core\Domains\Billing\Service\ServiceSearcher;
use Core\Domains\Billing\Service\ServiceTypeEnum;

readonly class CreateOtherServiceCommand
{
    public function __construct(
        private ServiceCatalogService $serviceService,
        private ServiceFactory        $serviceFactory,
    )
    {
    }

    public function execute(int $periodId): void
    {
        $service = $this->serviceService->search(
            ServiceSearcher::make()
                ->setPeriodId($periodId)
                ->setActive(true)
                ->setType(ServiceTypeEnum::OTHER),
        )->getItems()->first();

        if ($service !== null) {
            return;
        }

        $service = $this->serviceFactory->makeDefault()
            ->setPeriodId($periodId)
            ->setType(ServiceTypeEnum::OTHER)
            ->setName(ServiceTypeEnum::OTHER->name())
            ->setCost(0)
        ;

        $this->serviceService->save($service);
    }
}
