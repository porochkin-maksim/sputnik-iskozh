<?php declare(strict_types=1);

namespace Tests\Unit\App\Files;

use Core\App\Files\FileCleanupService;
use Core\Domains\Files\FileCollection;
use Core\Domains\Files\FileEntity;
use Core\Domains\Files\FileSearchResponse;
use Core\Domains\Files\FileService;
use Core\Domains\Files\FileTypeEnum;
use Tests\TestCase;

class FileCleanupServiceTest extends TestCase
{
    private FileService        $fileService;
    private FileCleanupService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileService = $this->createMock(FileService::class);
        $this->service     = new FileCleanupService($this->fileService);
    }

    public function test_delete_by_type_and_related_id(): void
    {
        $file1 = (new FileEntity)->setId(1);
        $file2 = (new FileEntity)->setId(2);

        $response = new FileSearchResponse;
        $response->setItems(new FileCollection([$file1, $file2]));

        $this->fileService->expects($this->once())
            ->method('search')
            ->willReturn($response)
        ;

        $this->fileService->expects($this->exactly(2))
            ->method('deleteById')
            ->willReturnCallback(fn(?int $id) => match ($id) {
                1 => true,
                2 => true,
            })
        ;

        $this->service->deleteByTypeAndRelatedId(FileTypeEnum::NEWS, 5);
    }

    public function test_delete_by_type_no_files(): void
    {
        $response = new FileSearchResponse;
        $response->setItems(new FileCollection);

        $this->fileService->method('search')->willReturn($response);

        $this->fileService->expects($this->never())->method('deleteById');

        $this->service->deleteByTypeAndRelatedId(FileTypeEnum::NEWS, 5);
    }
}
