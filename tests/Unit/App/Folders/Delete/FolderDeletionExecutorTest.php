<?php declare(strict_types=1);

namespace Tests\Unit\App\Folders\Delete;

use Core\App\Folders\Delete\FolderDeletionExecutor;
use Core\Domains\Files\FileCollection;
use Core\Domains\Files\FileEntity;
use Core\Domains\Files\FileSearchResponse;
use Core\Domains\Files\FileService;
use Core\Domains\Folders\FolderService;
use Tests\TestCase;

class FolderDeletionExecutorTest extends TestCase
{
    private FolderService          $folderService;
    private FileService            $fileService;
    private FolderDeletionExecutor $executor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->folderService = $this->createMock(FolderService::class);
        $this->fileService   = $this->createMock(FileService::class);
        $this->executor      = new FolderDeletionExecutor(
            $this->folderService,
            $this->fileService,
        );
    }

    public function test_execute_deletes_folders_and_files(): void
    {
        $file1 = (new FileEntity)->setId(10);
        $file2 = (new FileEntity)->setId(20);

        $fileResponse = new FileSearchResponse;
        $fileResponse->setItems(new FileCollection([$file1, $file2]));

        $emptyResponse = new FileSearchResponse;
        $emptyResponse->setItems(new FileCollection);

        $this->fileService->expects($this->exactly(2))
            ->method('search')
            ->willReturn($fileResponse, $emptyResponse)
        ;

        $this->fileService->expects($this->exactly(2))
            ->method('deleteById')
            ->willReturn(true)
        ;

        $this->folderService->expects($this->exactly(2))
            ->method('deleteDirectById')
            ->willReturn(true)
        ;

        $result = $this->executor->execute([1, 2]);

        $this->assertTrue($result);
    }
}
