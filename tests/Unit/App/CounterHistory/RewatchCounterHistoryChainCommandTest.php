<?php declare(strict_types=1);

namespace Tests\Unit\App\CounterHistory;

use Core\App\CounterHistory\RewatchCounterHistoryChainCommand;
use Core\App\CounterHistory\RewatchCounterHistoryChainInput;
use Core\Contracts\EventDispatcherInterface;
use Core\Domains\CounterHistory\CounterHistoryCollection;
use Core\Domains\CounterHistory\CounterHistoryEntity;
use Core\Domains\CounterHistory\CounterHistorySearchResponse;
use Core\Domains\CounterHistory\CounterHistoryService;
use Tests\TestCase;

class RewatchCounterHistoryChainCommandTest extends TestCase
{
    private CounterHistoryService             $counterHistoryService;
    private EventDispatcherInterface          $eventDispatcher;
    private RewatchCounterHistoryChainCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->counterHistoryService = $this->createMock(CounterHistoryService::class);
        $this->eventDispatcher       = $this->createMock(EventDispatcherInterface::class);

        $this->command = new RewatchCounterHistoryChainCommand(
            $this->counterHistoryService,
            $this->eventDispatcher,
        );
    }

    public function test_execute_rewatches_chain(): void
    {
        $h1 = new CounterHistoryEntity;
        $h1->setId(1)->setValue(100);

        $h2 = new CounterHistoryEntity;
        $h2->setId(2)->setValue(200);

        $response = new CounterHistorySearchResponse;
        $response->setItems(new CounterHistoryCollection([$h1, $h2]));

        $this->counterHistoryService->expects($this->once())
            ->method('search')
            ->willReturn($response)
        ;

        $this->counterHistoryService->expects($this->exactly(2))
            ->method('save')
            ->willReturnCallback(fn(CounterHistoryEntity $e) => $e)
        ;

        $this->eventDispatcher->expects($this->once())->method('dispatch');

        $this->command->execute(new RewatchCounterHistoryChainInput(1));
    }

    public function test_execute_does_not_dispatch_when_empty(): void
    {
        $response = new CounterHistorySearchResponse;
        $response->setItems(new CounterHistoryCollection);

        $this->counterHistoryService->expects($this->once())
            ->method('search')
            ->willReturn($response)
        ;

        $this->counterHistoryService->expects($this->never())->method('save');
        $this->eventDispatcher->expects($this->never())->method('dispatch');

        $this->command->execute(new RewatchCounterHistoryChainInput(1));
    }
}
