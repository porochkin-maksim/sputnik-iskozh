<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Period;

use Core\App\Billing\Period\GetListCommand;
use Core\Domains\Billing\Period\PeriodCollection;
use Core\Domains\Billing\Period\PeriodSearcher;
use Core\Domains\Billing\Period\PeriodSearchResponse;
use Core\Domains\Billing\Period\PeriodService;
use Tests\TestCase;

class GetListCommandTest extends TestCase
{
    private PeriodService  $periodService;
    private GetListCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->periodService = $this->createMock(PeriodService::class);

        $this->command = new GetListCommand(
            $this->periodService,
        );
    }

    public function test_execute_returns_search_response(): void
    {
        $response = new PeriodSearchResponse;
        $response->setItems(new PeriodCollection);

        $this->periodService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(PeriodSearcher::class))
            ->willReturn($response)
        ;

        $result = $this->command->execute();

        $this->assertSame($response, $result);
    }
}
