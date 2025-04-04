<?php declare(strict_types=1);

namespace Core\Domains\Billing\Jobs;

use Core\Domains\Billing\Service\Enums\ServiceTypeEnum;
use Core\Domains\Billing\Service\Models\ServiceSearcher;
use Core\Domains\Billing\Service\ServiceLocator;
use Core\Queue\QueueEnum;
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

    public function handle(): void
    {
        $serviceSearcher = new ServiceSearcher();
        $serviceSearcher
            ->setPeriodId($this->periodId)
            ->setActive(true)
            ->setType(ServiceTypeEnum::OTHER)
        ;
        $service = ServiceLocator::ServiceService()->search($serviceSearcher)->getItems()->first();

        if ( ! $service) {
            $service = ServiceLocator::ServiceFactory()->makeDefault();
            $service
                ->setPeriodId($this->periodId)
                ->setType(ServiceTypeEnum::OTHER)
                ->setName(ServiceTypeEnum::OTHER->name())
                ->setCost(0)
            ;
            ServiceLocator::ServiceService()->save($service);
        }
    }
}
