<?php declare(strict_types=1);

namespace Tests\Unit\App\CounterHistory;

use Core\App\CounterHistory\LinkCounterHistoriesCommand;
use Core\Contracts\EventDispatcherInterface;
use Core\Domains\CounterHistory\CounterHistoryEntity;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Domains\CounterHistory\Events\CounterHistoriesLinked;
use Tests\TestCase;

class LinkCounterHistoriesCommandTest extends TestCase
{
    private CounterHistoryService       $counterHistoryService;
    private EventDispatcherInterface    $eventDispatcher;
    private LinkCounterHistoriesCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->counterHistoryService = $this->createMock(CounterHistoryService::class);
        $this->eventDispatcher       = $this->createMock(EventDispatcherInterface::class);

        $this->command = new LinkCounterHistoriesCommand(
            $this->counterHistoryService,
            $this->eventDispatcher,
        );
    }

    public function test_execute_links_histories(): void
    {
        $newerHistory = new CounterHistoryEntity;
        $newerHistory->setId(2)->setPreviousId(null);

        $olderHistory = new CounterHistoryEntity;
        $olderHistory->setId(1);

        $this->counterHistoryService->expects($this->exactly(2))
            ->method('getById')
            ->willReturnMap([[1, $olderHistory], [2, $newerHistory]])
        ;

        $this->counterHistoryService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(CounterHistoryEntity $e) => $e->getId() === 2))
        ;

        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(CounterHistoriesLinked::class))
        ;

        $this->command->execute(1, 2);
    }

    public function test_execute_returns_early_when_both_null(): void
    {
        $this->counterHistoryService->expects($this->exactly(2))
            ->method('getById')
            ->willReturn(null)
        ;

        $this->counterHistoryService->expects($this->never())->method('save');
        $this->eventDispatcher->expects($this->never())->method('dispatch');

        $this->command->execute(999, 888);
    }

    public function test_execute_returns_early_when_newer_null(): void
    {
        $olderHistory = new CounterHistoryEntity;
        $olderHistory->setId(1);

        $this->counterHistoryService->expects($this->exactly(2))
            ->method('getById')
            ->willReturnMap([[1, $olderHistory], [2, null]])
        ;

        $this->counterHistoryService->expects($this->never())->method('save');
        $this->eventDispatcher->expects($this->never())->method('dispatch');

        $this->command->execute(1, 2);
    }
}
