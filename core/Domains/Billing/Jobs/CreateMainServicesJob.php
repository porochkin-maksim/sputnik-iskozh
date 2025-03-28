<?php declare(strict_types=1);

namespace Core\Domains\Billing\Jobs;

use Core\Domains\Billing\Service\Enums\ServiceTypeEnum;
use Core\Domains\Billing\Service\ServiceLocator;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Queue\QueueEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateMainServicesJob implements ShouldQueue
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
        $service = ServiceLocator::ServiceFactory()->makeDefault();
        $service
            ->setPeriodId($this->periodId)
            ->setType(ServiceTypeEnum::MEMBERSHIP_FEE)
            ->setName(ServiceTypeEnum::MEMBERSHIP_FEE->name())
            ->setCost(0);

        $service = ServiceLocator::ServiceService()->save($service);

        HistoryChangesLocator::HistoryChangesService()->writeToHistory(
            Event::CREATE,
            HistoryType::PERIOD,
            $this->periodId,
            HistoryType::SERVICE,
            $service->getId(),
        );

        $service = ServiceLocator::ServiceFactory()->makeDefault();
        $service
            ->setPeriodId($this->periodId)
            ->setType(ServiceTypeEnum::ELECTRIC_TARIFF)
            ->setName(ServiceTypeEnum::ELECTRIC_TARIFF->name())
            ->setCost(0);

        $service = ServiceLocator::ServiceService()->save($service);

        HistoryChangesLocator::HistoryChangesService()->writeToHistory(
            Event::CREATE,
            HistoryType::PERIOD,
            $this->periodId,
            HistoryType::SERVICE,
            $service->getId(),
        );

        $service = ServiceLocator::ServiceFactory()->makeDefault();
        $service
            ->setPeriodId($this->periodId)
            ->setType(ServiceTypeEnum::OTHER)
            ->setName(ServiceTypeEnum::OTHER->name())
            ->setCost(0);

        ServiceLocator::ServiceService()->save($service);
    }
}
