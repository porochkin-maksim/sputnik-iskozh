<?php declare(strict_types=1);

namespace Tests\Unit\App\CounterHistory;

use Core\App\CounterHistory\CreateCounterClaimCommand;
use Core\Contracts\DbServiceInterface;
use Core\Contracts\EventDispatcherInterface;
use Core\Domains\CounterHistory\CounterHistoryEntity;
use Core\Domains\CounterHistory\CounterHistoryService;
use Tests\TestCase;

class CreateCounterClaimCommandTest extends TestCase
{
    private DbServiceInterface        $dbService;
    private CounterHistoryService     $counterHistoryService;
    private EventDispatcherInterface  $eventDispatcher;
    private CreateCounterClaimCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dbService             = $this->createMock(DbServiceInterface::class);
        $this->counterHistoryService = $this->createMock(CounterHistoryService::class);
        $this->eventDispatcher       = $this->createMock(EventDispatcherInterface::class);

        $this->command = new CreateCounterClaimCommand(
            $this->dbService,
            $this->counterHistoryService,
            $this->eventDispatcher,
        );
    }

    public function test_execute_confirms_and_dispatches(): void
    {
        $history = new CounterHistoryEntity;
        $history->setId(1)->setIsVerified(false);

        $this->counterHistoryService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($history)
        ;

        $this->dbService->expects($this->once())
            ->method('transaction')
            ->willReturnCallback(fn(callable $callback) => $callback())
        ;

        $this->counterHistoryService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(CounterHistoryEntity $e) => $e->isVerified()))
        ;

        $this->eventDispatcher->expects($this->once())->method('dispatch');

        $result = $this->command->execute(1);
        $this->assertTrue($result);
    }

    public function test_execute_skips_when_history_not_found(): void
    {
        $this->counterHistoryService->expects($this->once())
            ->method('getById')
            ->with(999)
            ->willReturn(null)
        ;

        $this->dbService->expects($this->once())
            ->method('transaction')
            ->willReturnCallback(fn(callable $callback) => $callback())
        ;

        $this->counterHistoryService->expects($this->never())->method('save');
        $this->eventDispatcher->expects($this->once())->method('dispatch');

        $this->command->execute(999);
    }
}
