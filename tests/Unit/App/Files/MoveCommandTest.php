<?php declare(strict_types=1);

namespace Tests\Unit\App\Files;

use Core\App\Files\FileTransferService;
use Core\App\Files\MoveCommand;
use Core\Domains\Files\FileEntity;
use Core\Domains\Files\FileService;
use Core\Domains\Folders\FolderEntity;
use Core\Domains\Folders\FolderService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class MoveCommandTest extends TestCase
{
    private FileService         $fileService;
    private FolderService       $folderService;
    private FileTransferService $fileTransferService;
    private MoveCommand         $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileService         = $this->createMock(FileService::class);
        $this->folderService       = $this->createMock(FolderService::class);
        $this->fileTransferService = $this->createMock(FileTransferService::class);
        $this->command             = new MoveCommand(
            $this->fileService,
            $this->folderService,
            $this->fileTransferService,
        );
    }

    public function test_execute_moves_file(): void
    {
        $file   = (new FileEntity)->setId(1);
        $folder = (new FolderEntity)->setId(5);

        $this->fileService->method('getById')->with(1)->willReturn($file);
        $this->folderService->method('getById')->with(5)->willReturn($folder);

        $this->fileService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(FileEntity $f) => $f->getParentId() === 5))
        ;

        $result = $this->command->execute(1, 5, false);

        $this->assertTrue($result);
    }

    public function test_execute_copies_file(): void
    {
        $file   = (new FileEntity)->setId(1);
        $folder = (new FolderEntity)->setId(5);
        $copy   = (new FileEntity)->setId(null);

        $this->fileService->method('getById')->with(1)->willReturn($file);
        $this->folderService->method('getById')->with(5)->willReturn($folder);

        $this->fileTransferService->expects($this->once())
            ->method('copy')
            ->with($file)
            ->willReturn($copy)
        ;

        $this->fileService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(FileEntity $f) => $f->getParentId() === 5 && $f->getId() === null))
        ;

        $result = $this->command->execute(1, 5, true);

        $this->assertTrue($result);
    }

    public function test_execute_throws_when_file_not_found(): void
    {
        $this->fileService->method('getById')->willReturn(null);
        $this->folderService->method('getById')->willReturn(new FolderEntity);

        $this->expectException(ValidationException::class);

        $this->command->execute(999, 5, false);
    }

    public function test_execute_throws_when_folder_not_found(): void
    {
        $this->fileService->method('getById')->willReturn(new FileEntity);
        $this->folderService->method('getById')->willReturn(null);

        $this->expectException(ValidationException::class);

        $this->command->execute(1, 999, false);
    }

    public function test_execute_throws_when_both_not_found(): void
    {
        $this->fileService->method('getById')->willReturn(null);
        $this->folderService->method('getById')->willReturn(null);

        try {
            $this->command->execute(999, 999, false);
        }
        catch (ValidationException $e) {
            $this->assertArrayHasKey('file', $e->errors);
            $this->assertArrayHasKey('folder', $e->errors);
        }
    }
}
