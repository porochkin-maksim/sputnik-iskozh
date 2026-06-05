<?php declare(strict_types=1);

namespace Tests\Unit\App\CounterHistory;

use Core\App\CounterHistory\ConfirmCounterHistoriesCommand;
use Core\App\CounterHistory\ConfirmCounterHistoriesValidator;
use Core\Contracts\DbServiceInterface;
use Core\Contracts\EventDispatcherInterface;
use Core\Domains\CounterHistory\CounterHistoryCollection;
use Core\Domains\CounterHistory\CounterHistoryEntity;
use Core\Domains\CounterHistory\CounterHistorySearchResponse;
use Core\Domains\CounterHistory\CounterHistoryService;
use Tests\TestCase;

class ConfirmCounterHistoriesCommandTest extends TestCase
{
    private DbServiceInterface               $dbService;
    private CounterHistoryService            $counterHistoryService;
    private ConfirmCounterHistoriesValidator $validator;
    private EventDispatcherInterface         $eventDispatcher;
    private ConfirmCounterHistoriesCommand   $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dbService             = $this->createMock(DbServiceInterface::class);
        $this->counterHistoryService = $this->createMock(CounterHistoryService::class);
        $this->validator             = $this->createMock(ConfirmCounterHistoriesValidator::class);
        $this->eventDispatcher       = $this->createMock(EventDispatcherInterface::class);

        $this->command = new ConfirmCounterHistoriesCommand(
            $this->dbService,
            $this->counterHistoryService,
            $this->validator,
            $this->eventDispatcher,
        );
    }

    public function test_execute_confirms_histories(): void
    {
        $this->validator->expects($this->once())->method('validate');

        $history = new CounterHistoryEntity;
        $history->setId(1)->setIsVerified(false);

        $response = new CounterHistorySearchResponse;
        $response->setItems(new CounterHistoryCollection([$history]));

        $this->counterHistoryService->expects($this->once())
            ->method('search')
            ->willReturn($response)
        ;

        $this->dbService->expects($this->once())
            ->method('transaction')
            ->willReturnCallback(fn(callable $callback) => $callback())
        ;

        $this->counterHistoryService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(CounterHistoryEntity $e) => $e->isVerified()))
            ->willReturn($history)
        ;

        $this->eventDispatcher->expects($this->once())->method('dispatch');

        $this->command->execute([1]);
    }

    public function test_execute_does_not_dispatch_when_none_found(): void
    {
        $this->validator->expects($this->once())->method('validate');

        $response = new CounterHistorySearchResponse;
        $response->setItems(new CounterHistoryCollection);

        $this->counterHistoryService->expects($this->once())
            ->method('search')
            ->willReturn($response)
        ;

        $this->dbService->expects($this->once())
            ->method('transaction')
            ->willReturnCallback(fn(callable $callback) => $callback())
        ;

        $this->counterHistoryService->expects($this->never())->method('save');
        $this->eventDispatcher->expects($this->never())->method('dispatch');

        $this->command->execute([1]);
    }
}
