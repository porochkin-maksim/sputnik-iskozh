<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Service;

use Core\App\Billing\Service\CreateOtherServiceCommand;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceCollection;
use Core\Domains\Billing\Service\ServiceEntity;
use Core\Domains\Billing\Service\ServiceFactory;
use Core\Domains\Billing\Service\ServiceSearcher;
use Core\Domains\Billing\Service\ServiceSearchResponse;
use Tests\TestCase;

class CreateOtherServiceCommandTest extends TestCase
{
    private ServiceCatalogService     $serviceService;
    private ServiceFactory            $serviceFactory;
    private CreateOtherServiceCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serviceService = $this->createMock(ServiceCatalogService::class);
        $this->serviceFactory = $this->createMock(ServiceFactory::class);

        $this->command = new CreateOtherServiceCommand(
            $this->serviceService,
            $this->serviceFactory,
        );
    }

    public function test_execute_skips_when_other_service_exists(): void
    {
        $periodId = 1;
        $existing = new ServiceEntity;
        $response = new ServiceSearchResponse;
        $response->setItems(new ServiceCollection([$existing]));

        $this->serviceService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(ServiceSearcher::class))
            ->willReturn($response)
        ;

        $this->serviceFactory->expects($this->never())->method('makeDefault');

        $this->command->execute($periodId);
    }

    public function test_execute_creates_other_service_when_not_exists(): void
    {
        $periodId = 1;
        $response = new ServiceSearchResponse;
        $response->setItems(new ServiceCollection);
        $default = new ServiceEntity;

        $this->serviceService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(ServiceSearcher::class))
            ->willReturn($response)
        ;

        $this->serviceFactory->expects($this->once())
            ->method('makeDefault')
            ->willReturn($default)
        ;

        $this->serviceService->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(ServiceEntity::class))
            ->willReturnCallback(fn(ServiceEntity $s) => $s->setId(1))
        ;

        $this->command->execute($periodId);
    }
}
