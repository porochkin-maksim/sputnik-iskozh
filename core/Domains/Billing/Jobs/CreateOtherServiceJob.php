<?php declare(strict_types=1);

namespace Core\Domains\Billing\Jobs;

use Core\Domains\Billing\Service\ServiceFactory;
use Core\Domains\Billing\Service\ServiceSearcher;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use App\Services\Queue\QueueEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Создаёт технологическую услугу с типом "прочее" для указанного периода
 */
class CreateOtherServiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $periodId,
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    public function handle(
        ServiceCatalogService $serviceService,
        ServiceFactory        $serviceFactory,
    ): void
    {
        $serviceSearcher = new ServiceSearcher();
        $serviceSearcher
            ->setPeriodId($this->periodId)
            ->setActive(true)
            ->setType(ServiceTypeEnum::OTHER)
        ;
        $service = $serviceService->search($serviceSearcher)->getItems()->first();

        if ( ! $service) {
            $service = $serviceFactory->makeDefault();
            $service
                ->setPeriodId($this->periodId)
                ->setType(ServiceTypeEnum::OTHER)
                ->setName(ServiceTypeEnum::OTHER->name())
                ->setCost(0)
            ;
            $serviceService->save($service);
        }
    }
}
