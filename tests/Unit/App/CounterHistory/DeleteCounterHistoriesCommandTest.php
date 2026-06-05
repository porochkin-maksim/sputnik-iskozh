<?php declare(strict_types=1);

namespace Tests\Unit\App\CounterHistory;

use Core\App\CounterHistory\ConfirmCounterHistoriesValidator;
use Core\App\CounterHistory\DeleteCounterHistoriesCommand;
use Core\Contracts\DbServiceInterface;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class DeleteCounterHistoriesCommandTest extends TestCase
{
    private DbServiceInterface               $dbService;
    private CounterHistoryService            $counterHistoryService;
    private ConfirmCounterHistoriesValidator $validator;
    private DeleteCounterHistoriesCommand    $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dbService             = $this->createMock(DbServiceInterface::class);
        $this->counterHistoryService = $this->createMock(CounterHistoryService::class);
        $this->validator             = $this->createMock(ConfirmCounterHistoriesValidator::class);

        $this->command = new DeleteCounterHistoriesCommand(
            $this->dbService,
            $this->counterHistoryService,
            $this->validator,
        );
    }

    public function test_execute_deletes_histories(): void
    {
        $this->validator->expects($this->once())->method('validate');

        $this->dbService->expects($this->once())
            ->method('transaction')
            ->willReturnCallback(fn(callable $callback) => $callback())
        ;

        $this->counterHistoryService->expects($this->exactly(2))
            ->method('deleteById')
            ->willReturnCallback(fn(int $id) => in_array($id, [1, 2], true))
        ;

        $this->command->execute([1, 2]);
    }

    public function test_execute_skips_when_empty(): void
    {
        $this->validator->expects($this->once())
            ->method('validate')
            ->willThrowException(new ValidationException(['ids' => ['error']]))
        ;

        $this->expectException(ValidationException::class);
        $this->command->execute([]);
    }
}
