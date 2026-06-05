<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Service;

use Core\App\Billing\Service\GetListCommand;
use Core\Domains\Billing\Period\PeriodSearcher;
use Core\Domains\Billing\Period\PeriodSearchResponse;
use Core\Domains\Billing\Period\PeriodService;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceSearcher;
use Core\Domains\Billing\Service\ServiceSearchResponse;
use Tests\TestCase;

class GetListCommandTest extends TestCase
{
    private ServiceCatalogService $serviceService;
    private PeriodService         $periodService;
    private GetListCommand        $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serviceService = $this->createMock(ServiceCatalogService::class);
        $this->periodService  = $this->createMock(PeriodService::class);

        $this->command = new GetListCommand(
            $this->serviceService,
            $this->periodService,
        );
    }

    public function test_execute_returns_services_and_periods(): void
    {
        $servicesResponse = new ServiceSearchResponse;
        $periodsResponse  = new PeriodSearchResponse;

        $this->serviceService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(ServiceSearcher::class))
            ->willReturn($servicesResponse)
        ;

        $this->periodService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(PeriodSearcher::class))
            ->willReturn($periodsResponse)
        ;

        $result = $this->command->execute();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('services', $result);
        $this->assertArrayHasKey('periods', $result);
        $this->assertSame($servicesResponse, $result['services']);
        $this->assertSame($periodsResponse, $result['periods']);
    }
}
