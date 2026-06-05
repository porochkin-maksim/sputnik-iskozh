<?php declare(strict_types=1);

namespace Tests\Unit\App\Folders\Delete;

use Core\App\Folders\Delete\FolderDeletionWalker;
use Core\Domains\Folders\FolderCollection;
use Core\Domains\Folders\FolderEntity;
use Core\Domains\Folders\FolderSearchResponse;
use Core\Domains\Folders\FolderService;
use Tests\TestCase;

class FolderDeletionWalkerTest extends TestCase
{
    private FolderService        $folderService;
    private FolderDeletionWalker $walker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->folderService = $this->createMock(FolderService::class);
        $this->walker        = new FolderDeletionWalker($this->folderService);
    }

    public function test_collect_folder_ids_flat(): void
    {
        $response = new FolderSearchResponse;
        $response->setItems(new FolderCollection);

        $this->folderService->method('search')->willReturn($response);

        $ids = $this->walker->collectFolderIds(1);

        $this->assertSame([1], $ids);
    }

    public function test_collect_folder_ids_nested(): void
    {
        $child1     = (new FolderEntity)->setId(2);
        $child2     = (new FolderEntity)->setId(3);
        $grandchild = (new FolderEntity)->setId(4);

        $rootResponse = new FolderSearchResponse;
        $rootResponse->setItems(new FolderCollection([$child1, $child2]));

        $child1Response = new FolderSearchResponse;
        $child1Response->setItems(new FolderCollection([$grandchild]));

        $leafResponse = new FolderSearchResponse;
        $leafResponse->setItems(new FolderCollection);

        $this->folderService->expects($this->exactly(4))
            ->method('search')
            ->willReturn($rootResponse, $child1Response, $leafResponse, $leafResponse)
        ;

        $ids = $this->walker->collectFolderIds(1);

        sort($ids);
        $this->assertSame([1, 2, 3, 4], $ids);
    }
}
