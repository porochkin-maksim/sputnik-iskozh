<?php declare(strict_types=1);

namespace Tests\Unit\App\Folders\Delete;

use Core\App\Folders\Delete\FolderDeletionExecutor;
use Core\App\Folders\Delete\FolderDeletionService;
use Core\App\Folders\Delete\FolderDeletionWalker;
use Core\Contracts\DbServiceInterface;
use Core\Domains\Folders\FolderEntity;
use Core\Domains\Folders\FolderService;
use Tests\TestCase;

class FolderDeletionServiceTest extends TestCase
{
    private FolderService          $folderService;
    private FolderDeletionWalker   $walker;
    private FolderDeletionExecutor $executor;
    private DbServiceInterface     $dbService;
    private FolderDeletionService  $deletionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->folderService   = $this->createMock(FolderService::class);
        $this->walker          = $this->createMock(FolderDeletionWalker::class);
        $this->executor        = $this->createMock(FolderDeletionExecutor::class);
        $this->dbService       = $this->createMock(DbServiceInterface::class);
        $this->deletionService = new FolderDeletionService(
            $this->folderService,
            $this->walker,
            $this->executor,
            $this->dbService,
        );
    }

    public function test_delete_by_id_success(): void
    {
        $folder = (new FolderEntity)->setId(1);

        $this->folderService->method('getById')->with(1)->willReturn($folder);

        $this->walker->method('collectFolderIds')->with(1)->willReturn([1, 2, 3]);

        $this->dbService->method('transaction')
            ->willReturnCallback(fn(callable $cb) => $cb())
        ;

        $this->executor->expects($this->once())
            ->method('execute')
            ->with([1, 2, 3])
            ->willReturn(true)
        ;

        $result = $this->deletionService->deleteById(1);

        $this->assertTrue($result);
    }

    public function test_delete_by_id_returns_false_when_folder_not_found(): void
    {
        $this->folderService->method('getById')->willReturn(null);

        $this->dbService->expects($this->never())->method('transaction');

        $result = $this->deletionService->deleteById(999);

        $this->assertFalse($result);
    }
}
