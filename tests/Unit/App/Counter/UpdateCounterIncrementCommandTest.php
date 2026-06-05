<?php declare(strict_types=1);

namespace Tests\Unit\App\Counter;

use Core\App\Counter\UpdateCounterIncrementCommand;
use Core\Contracts\DbServiceInterface;
use Core\Domains\Counter\CounterEntity;
use Core\Domains\Counter\CounterService;
use Tests\TestCase;

class UpdateCounterIncrementCommandTest extends TestCase
{
    private DbServiceInterface            $dbService;
    private CounterService                $counterService;
    private UpdateCounterIncrementCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dbService      = $this->createMock(DbServiceInterface::class);
        $this->counterService = $this->createMock(CounterService::class);
        $this->command        = new UpdateCounterIncrementCommand($this->dbService, $this->counterService);
    }

    public function test_execute_updates_increment(): void
    {
        $counter = new CounterEntity;
        $counter->setId(1)->setAccountId(100)->setIncrement(1);

        $this->counterService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($counter)
        ;

        $this->dbService->expects($this->once())
            ->method('transaction')
            ->willReturnCallback(fn(callable $callback) => $callback())
        ;

        $this->counterService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(CounterEntity $c) => $c->getIncrement() === 5))
        ;

        $this->command->execute(1, 5, 100);
    }

    public function test_execute_returns_early_when_counter_not_found(): void
    {
        $this->counterService->expects($this->once())
            ->method('getById')
            ->with(999)
            ->willReturn(null)
        ;

        $this->dbService->expects($this->never())->method('transaction');
        $this->counterService->expects($this->never())->method('save');

        $this->command->execute(999, 5, 100);
    }

    public function test_execute_returns_early_when_account_mismatch(): void
    {
        $counter = new CounterEntity;
        $counter->setId(1)->setAccountId(200);

        $this->counterService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($counter)
        ;

        $this->dbService->expects($this->never())->method('transaction');
        $this->counterService->expects($this->never())->method('save');

        $this->command->execute(1, 5, 100);
    }
}
