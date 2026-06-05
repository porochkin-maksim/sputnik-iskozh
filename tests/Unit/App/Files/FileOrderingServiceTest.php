<?php declare(strict_types=1);

namespace Tests\Unit\App\Files;

use Core\App\Files\FileOrderingService;
use Core\Domains\Files\FileCollection;
use Core\Domains\Files\FileEntity;
use Core\Domains\Files\FileSearchResponse;
use Core\Domains\Files\FileService;
use Core\Domains\Files\FileTypeEnum;
use Tests\TestCase;

class FileOrderingServiceTest extends TestCase
{
    private FileService         $fileService;
    private FileOrderingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileService = $this->createMock(FileService::class);
        $this->service     = new FileOrderingService($this->fileService);
    }

    public function test_save_order_index_moves_down(): void
    {
        $fileA  = (new FileEntity)->setId(1)->setOrder(0);
        $fileB  = (new FileEntity)->setId(2)->setOrder(1);
        $fileC  = (new FileEntity)->setId(3)->setOrder(2);
        $fileD  = (new FileEntity)->setId(4)->setOrder(3);
        $target = (new FileEntity)->setId(3)->setType(FileTypeEnum::NEWS)->setRelatedId(5);

        $response = new FileSearchResponse;
        $response->setItems(new FileCollection([$fileA, $fileB, $fileC, $fileD]));

        $this->fileService->method('search')->willReturn($response);

        $savedOrders = [];
        $this->fileService->expects($this->exactly(4))
            ->method('save')
            ->willReturnCallback(function (FileEntity $f) use (&$savedOrders) {
                $savedOrders[$f->getId()] = $f->getOrder();

                return $f;
            })
        ;

        $this->service->saveFileOrderIndex($target, 3);

        $this->assertSame(0, $savedOrders[1]);
        $this->assertSame(1, $savedOrders[2]);
        $this->assertSame(2, $savedOrders[4]);
        $this->assertSame(3, $savedOrders[3]);
    }

    public function test_save_order_index_moves_up(): void
    {
        $fileA  = (new FileEntity)->setId(1)->setOrder(0);
        $fileB  = (new FileEntity)->setId(2)->setOrder(1);
        $fileC  = (new FileEntity)->setId(3)->setOrder(2);
        $fileD  = (new FileEntity)->setId(4)->setOrder(3);
        $target = (new FileEntity)->setId(2)->setType(FileTypeEnum::NEWS)->setRelatedId(5);

        $response = new FileSearchResponse;
        $response->setItems(new FileCollection([$fileA, $fileB, $fileC, $fileD]));

        $this->fileService->method('search')->willReturn($response);

        $savedOrders = [];
        $this->fileService->expects($this->exactly(4))
            ->method('save')
            ->willReturnCallback(function (FileEntity $f) use (&$savedOrders) {
                $savedOrders[$f->getId()] = $f->getOrder();

                return $f;
            })
        ;

        $this->service->saveFileOrderIndex($target, 0);

        $this->assertSame(1, $savedOrders[1]);
        $this->assertSame(0, $savedOrders[2]);
        $this->assertSame(2, $savedOrders[3]);
        $this->assertSame(3, $savedOrders[4]);
    }

    public function test_save_order_index_single_file(): void
    {
        $file   = (new FileEntity)->setId(1)->setOrder(0);
        $target = (new FileEntity)->setId(1)->setType(FileTypeEnum::NEWS)->setRelatedId(5);

        $response = new FileSearchResponse;
        $response->setItems(new FileCollection([$file]));

        $this->fileService->method('search')->willReturn($response);

        $this->fileService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(FileEntity $f) => $f->getOrder() === 0))
        ;

        $this->service->saveFileOrderIndex($target, 0);
    }
}
