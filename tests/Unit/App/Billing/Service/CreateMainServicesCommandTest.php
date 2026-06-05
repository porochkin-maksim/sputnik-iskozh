<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Service;

use Core\App\Billing\Service\CreateMainServicesCommand;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceEntity;
use Core\Domains\Billing\Service\ServiceFactory;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use Core\Domains\HistoryChanges\Event;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Core\Domains\HistoryChanges\HistoryType;
use Tests\TestCase;

class CreateMainServicesCommandTest extends TestCase
{
    private ServiceCatalogService     $serviceService;
    private ServiceFactory            $serviceFactory;
    private HistoryChangesService     $historyChangesService;
    private CreateMainServicesCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serviceService        = $this->createMock(ServiceCatalogService::class);
        $this->serviceFactory        = $this->createMock(ServiceFactory::class);
        $this->historyChangesService = $this->createMock(HistoryChangesService::class);

        $this->command = new CreateMainServicesCommand(
            $this->serviceService,
            $this->serviceFactory,
            $this->historyChangesService,
        );
    }

    public function test_execute_skips_existing_services(): void
    {
        $periodId = 1;

        $this->serviceService->expects($this->exactly(5))
            ->method('getByPeriodIdAndType')
            ->willReturn(new ServiceEntity)
        ;

        $this->serviceFactory->expects($this->never())->method('makeDefault');

        $this->command->execute($periodId);
    }

    public function test_execute_creates_all_services(): void
    {
        $periodId = 1;
        $default  = new ServiceEntity;
        $saved    = (new ServiceEntity)->setId(1);

        $this->serviceService->expects($this->exactly(5))
            ->method('getByPeriodIdAndType')
            ->willReturn(null)
        ;

        $this->serviceFactory->expects($this->exactly(5))
            ->method('makeDefault')
            ->willReturn($default)
        ;

        $this->serviceService->expects($this->exactly(5))
            ->method('save')
            ->willReturn($saved)
        ;

        $this->historyChangesService->expects($this->exactly(5))
            ->method('writeToHistory')
            ->with(
                Event::CREATE,
                HistoryType::PERIOD,
                $periodId,
                HistoryType::SERVICE,
                1,
            )
        ;

        $this->command->execute($periodId);
    }

    public function test_execute_mixes_existing_and_new(): void
    {
        $periodId   = 1;
        $caseCounts = [
            'MEMBERSHIP_FEE'  => 'existing',
            'ELECTRIC_TARIFF' => 'new',
            'OTHER'           => 'existing',
            'DEBT'            => 'new',
            'ADVANCE_PAYMENT' => 'existing',
        ];

        $this->serviceService->expects($this->exactly(5))
            ->method('getByPeriodIdAndType')
            ->willReturnCallback(fn(int $pid, ServiceTypeEnum $type) => match ($caseCounts[$type->name]) {
                'existing' => new ServiceEntity,
                'new'      => null,
            })
        ;

        $this->serviceFactory->expects($this->exactly(2))
            ->method('makeDefault')
            ->willReturn(new ServiceEntity)
        ;

        $saved = (new ServiceEntity)->setId(1);

        $this->serviceService->expects($this->exactly(2))
            ->method('save')
            ->willReturn($saved)
        ;

        $this->historyChangesService->expects($this->exactly(2))
            ->method('writeToHistory')
            ->with(
                Event::CREATE,
                HistoryType::PERIOD,
                $periodId,
                HistoryType::SERVICE,
                1,
            )
        ;

        $this->command->execute($periodId);
    }
}
