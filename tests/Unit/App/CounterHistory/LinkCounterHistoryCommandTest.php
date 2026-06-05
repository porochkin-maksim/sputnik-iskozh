<?php declare(strict_types=1);

namespace Tests\Unit\App\CounterHistory;

use Core\App\CounterHistory\LinkCounterHistoryCommand;
use Core\App\CounterHistory\LinkCounterHistoryValidator;
use Core\Contracts\DbServiceInterface;
use Core\Domains\CounterHistory\CounterHistoryCollection;
use Core\Domains\CounterHistory\CounterHistoryEntity;
use Core\Domains\CounterHistory\CounterHistorySearchResponse;
use Core\Domains\CounterHistory\CounterHistoryService;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class LinkCounterHistoryCommandTest extends TestCase
{
    private DbServiceInterface          $dbService;
    private CounterHistoryService       $counterHistoryService;
    private LinkCounterHistoryValidator $validator;
    private LinkCounterHistoryCommand   $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dbService             = $this->createMock(DbServiceInterface::class);
        $this->counterHistoryService = $this->createMock(CounterHistoryService::class);
        $this->validator             = $this->createMock(LinkCounterHistoryValidator::class);

        $this->command = new LinkCounterHistoryCommand(
            $this->dbService,
            $this->counterHistoryService,
            $this->validator,
        );
    }

    public function test_execute_links_history_to_counter(): void
    {
        $this->validator->expects($this->once())->method('validate');

        $history = new CounterHistoryEntity;
        $history->setId(1);

        $this->counterHistoryService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($history)
        ;

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

        $this->counterHistoryService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(CounterHistoryEntity $e) => $e->getCounterId() === 5))
        ;

        $this->command->execute(1, 5);
    }

    public function test_execute_throws_when_history_not_found(): void
    {
        $this->validator->expects($this->once())->method('validate');

        $this->counterHistoryService->expects($this->once())
            ->method('getById')
            ->with(999)
            ->willReturn(null)
        ;

        $this->expectException(HttpException::class);
        $this->command->execute(999, 5);
    }
}
