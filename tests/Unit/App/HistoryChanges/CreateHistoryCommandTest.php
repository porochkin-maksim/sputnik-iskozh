<?php declare(strict_types=1);

namespace Tests\Unit\App\HistoryChanges;

use Core\App\HistoryChanges\CreateHistoryCommand;
use Core\App\HistoryChanges\CreateHistoryInput;
use Core\Domains\HistoryChanges\HistoryChangesEntity;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Tests\TestCase;

class CreateHistoryCommandTest extends TestCase
{
    private HistoryChangesService $historyChangesService;
    private CreateHistoryCommand  $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->historyChangesService = $this->createMock(HistoryChangesService::class);
        $this->command               = new CreateHistoryCommand($this->historyChangesService);
    }

    public function test_execute_saves_history(): void
    {
        $entity = new HistoryChangesEntity;

        $this->historyChangesService->expects($this->once())
            ->method('save')
            ->with($entity)
        ;

        $input = new CreateHistoryInput($entity);

        $this->command->execute($input);
    }
}
