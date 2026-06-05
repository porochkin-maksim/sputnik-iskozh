<?php declare(strict_types=1);

namespace Tests\Unit\App\Folders;

use Core\App\Folders\Delete\FolderDeletionService;
use Core\App\Folders\DeleteCommand;
use Core\Domains\Folders\FolderEntity;
use Core\Domains\Folders\FolderService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class DeleteCommandTest extends TestCase
{
    private FolderService         $folderService;
    private FolderDeletionService $folderDeletionService;
    private DeleteCommand         $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->folderService         = $this->createMock(FolderService::class);
        $this->folderDeletionService = $this->createMock(FolderDeletionService::class);
        $this->command               = new DeleteCommand(
            $this->folderService,
            $this->folderDeletionService,
        );
    }

    public function test_execute_deletes_folder(): void
    {
        $this->folderService->method('getById')->with(1)->willReturn(new FolderEntity);

        $this->folderDeletionService->expects($this->once())
            ->method('deleteById')
            ->with(1)
            ->willReturn(true)
        ;

        $result = $this->command->execute(1);

        $this->assertTrue($result);
    }

    public function test_execute_throws_when_folder_not_found(): void
    {
        $this->folderService->method('getById')->willReturn(null);

        $this->folderDeletionService->expects($this->never())->method('deleteById');

        $this->expectException(ValidationException::class);

        $this->command->execute(999);
    }
}
