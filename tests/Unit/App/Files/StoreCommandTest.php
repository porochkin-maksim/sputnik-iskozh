<?php declare(strict_types=1);

namespace Tests\Unit\App\Files;

use Core\App\Files\FileTransferService;
use Core\App\Files\StoreCommand;
use Core\App\Files\StoreValidator;
use Core\Domains\Folders\FolderEntity;
use Core\Domains\Folders\FolderService;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class StoreCommandTest extends TestCase
{
    private FolderService       $folderService;
    private FileTransferService $fileTransferService;
    private StoreValidator      $storeValidator;
    private StoreCommand        $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->folderService       = $this->createMock(FolderService::class);
        $this->fileTransferService = $this->createMock(FileTransferService::class);
        $this->storeValidator      = $this->createMock(StoreValidator::class);
        $this->command             = new StoreCommand(
            $this->folderService,
            $this->fileTransferService,
            $this->storeValidator,
        );
    }

    public function test_execute_stores_files(): void
    {
        $files = [new UploadedFile('test.pdf', '/tmp/test.pdf', 'pdf', 100, 'content')];

        $this->storeValidator->method('validate');

        $this->fileTransferService->expects($this->once())
            ->method('storeAndSave')
            ->with($files, null, 'uploads', null)
        ;

        $result = $this->command->execute($files, 'uploads');

        $this->assertTrue($result);
    }

    public function test_execute_with_parent_id(): void
    {
        $files = [new UploadedFile('test.pdf', '/tmp/test.pdf', 'pdf', 100, 'content')];

        $this->storeValidator->method('validate');

        $this->folderService->method('getById')->with(1)->willReturn(new FolderEntity);

        $this->fileTransferService->expects($this->once())
            ->method('storeAndSave')
            ->with($files, 1, 'uploads', null)
        ;

        $result = $this->command->execute($files, 'uploads', 1);

        $this->assertTrue($result);
    }

    public function test_execute_throws_when_parent_not_found(): void
    {
        $this->storeValidator->method('validate');

        $this->folderService->method('getById')->willReturn(null);

        $this->fileTransferService->expects($this->never())->method('storeAndSave');

        $this->expectException(ValidationException::class);

        $this->command->execute([new UploadedFile('t.pdf', '/tmp/t.pdf', 'pdf', 1, '')], 'dir', 999);
    }

    public function test_execute_validates_before_any_action(): void
    {
        $this->storeValidator->expects($this->once())
            ->method('validate')
            ->willThrowException(new ValidationException(['files' => ['error']]))
        ;

        $this->folderService->expects($this->never())->method('getById');

        $this->expectException(ValidationException::class);

        $this->command->execute([], 'dir');
    }
}
