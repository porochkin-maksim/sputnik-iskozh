<?php declare(strict_types=1);

namespace Tests\Unit\App\CounterHistory;

use Core\App\CounterHistory\DeleteCounterHistoryFileCommand;
use Core\Domains\Counter\FileService;
use Tests\TestCase;

class DeleteCounterHistoryFileCommandTest extends TestCase
{
    private FileService                     $fileService;
    private DeleteCounterHistoryFileCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileService = $this->createMock(FileService::class);
        $this->command     = new DeleteCounterHistoryFileCommand($this->fileService);
    }

    public function test_execute_deletes_file(): void
    {
        $this->fileService->expects($this->once())
            ->method('deleteHistoryById')
            ->with(1)
        ;

        $this->command->execute(1);
    }
}
