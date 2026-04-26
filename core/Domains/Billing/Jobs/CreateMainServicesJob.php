<?php declare(strict_types=1);

namespace Core\Domains\Billing\Jobs;

use Core\Domains\Billing\Service\ServiceFactory;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use Core\Domains\HistoryChanges\Event;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Core\Domains\HistoryChanges\HistoryType;
use App\Services\Queue\QueueEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * При создании периода создаем основные услуги
 */
class CreateMainServicesJob implements ShouldQueue
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
        HistoryChangesService $historyChangesService,
    ): void
    {
        $cases = [
            ServiceTypeEnum::MEMBERSHIP_FEE,
            ServiceTypeEnum::ELECTRIC_TARIFF,
            ServiceTypeEnum::OTHER,
            ServiceTypeEnum::DEBT,
            ServiceTypeEnum::ADVANCE_PAYMENT,
        ];
        foreach ($cases as $case) {
            $service = $serviceService->getByPeriodIdAndType($this->periodId, $case);
            if ($service) {
                continue;
            }

            $service = $serviceFactory->makeDefault();
            $service
                ->setPeriodId($this->periodId)
                ->setType($case)
                ->setName($case->name())
                ->setCost(0)
            ;

            $service = $serviceService->save($service);

            $historyChangesService->writeToHistory(
                Event::CREATE,
                HistoryType::PERIOD,
                $this->periodId,
                HistoryType::SERVICE,
                $service->getId(),
            );
        }
    }
}
