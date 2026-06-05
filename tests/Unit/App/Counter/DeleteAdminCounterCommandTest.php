<?php declare(strict_types=1);

namespace Tests\Unit\App\Counter;

use Core\App\Counter\DeleteAdminCounterCommand;
use Core\Contracts\DbServiceInterface;
use Core\Domains\Counter\CounterService;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Tests\TestCase;

class DeleteAdminCounterCommandTest extends TestCase
{
    private DbServiceInterface        $dbService;
    private CounterService            $counterService;
    private HistoryChangesService     $historyChangesService;
    private DeleteAdminCounterCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dbService             = $this->createMock(DbServiceInterface::class);
        $this->counterService        = $this->createMock(CounterService::class);
        $this->historyChangesService = $this->createMock(HistoryChangesService::class);
        $this->command               = new DeleteAdminCounterCommand(
            $this->dbService,
            $this->counterService,
            $this->historyChangesService,
        );
    }

    public function test_execute_deletes_without_comment(): void
    {
        $this->dbService->expects($this->once())
            ->method('transaction')
            ->willReturnCallback(fn(callable $callback) => $callback())
        ;

        $this->counterService->expects($this->once())
            ->method('deleteById')
            ->with(1)
            ->willReturn(true)
        ;

        $this->historyChangesService->expects($this->never())->method('writeToHistory');

        $result = $this->command->execute(1, null);

        $this->assertTrue($result);
    }

    public function test_execute_deletes_with_comment(): void
    {
        $this->dbService->expects($this->once())
            ->method('transaction')
            ->willReturnCallback(fn(callable $callback) => $callback())
        ;

        $this->counterService->expects($this->once())
            ->method('deleteById')
            ->with(1)
            ->willReturn(true)
        ;

        $this->historyChangesService->expects($this->once())
            ->method('writeToHistory')
        ;

        $result = $this->command->execute(1, 'test comment');

        $this->assertTrue($result);
    }

    public function test_execute_returns_false_when_delete_fails(): void
    {
        $this->dbService->expects($this->once())
            ->method('transaction')
            ->willReturnCallback(fn(callable $callback) => $callback())
        ;

        $this->counterService->expects($this->once())
            ->method('deleteById')
            ->with(999)
            ->willReturn(false)
        ;

        $this->historyChangesService->expects($this->never())->method('writeToHistory');

        $result = $this->command->execute(999, null);

        $this->assertFalse($result);
    }
}
