<?php declare(strict_types=1);

namespace Tests\Unit\App\Files;

use Core\App\Files\FileTransferService;
use Core\Contracts\FileStorageInterface;
use Core\Contracts\StringServiceInterface;
use Core\Domains\Files\FileEntity;
use Core\Domains\Files\FileService;
use Core\Domains\Files\FileTypeEnum;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use Tests\TestCase;

class FileTransferServiceTest extends TestCase
{
    private FileService            $fileService;
    private FileStorageInterface   $storage;
    private StringServiceInterface $stringGenerator;
    private FileTransferService    $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileService     = $this->createMock(FileService::class);
        $this->storage         = $this->createMock(FileStorageInterface::class);
        $this->stringGenerator = $this->createMock(StringServiceInterface::class);
        $this->service         = new FileTransferService(
            $this->fileService,
            $this->storage,
            $this->stringGenerator,
        );
    }

    public function test_copy_creates_copy(): void
    {
        $file = (new FileEntity)->setId(5)->setPath('uploads/old/file.pdf')->setExt('pdf');

        $this->stringGenerator->method('random')->with(8)->willReturn('abc12345');
        $this->storage->method('exists')->willReturn(false);
        $this->stringGenerator->method('replace')
            ->with('file.pdf', 'abc12345.pdf', 'uploads/old/file.pdf')
            ->willReturn('uploads/old/abc12345.pdf')
        ;
        $this->stringGenerator->method('normalizePath')
            ->willReturnCallback(fn(string $p) => preg_replace('#/{2,}#', '/', $p))
        ;

        $this->storage->expects($this->once())
            ->method('copy')
            ->with('uploads/old/file.pdf', 'uploads/old/abc12345.pdf')
        ;

        $result = $this->service->copy($file);

        $this->assertNull($result->getId());
        $this->assertSame('uploads/old/abc12345.pdf', $result->getPath());
        $this->assertSame('pdf', $result->getExt());
    }

    public function test_replace_same_extension_copies_content(): void
    {
        $file        = (new FileEntity)->setId(1)->setPath('target/file.pdf')->setExt('pdf');
        $replaceFile = (new FileEntity)->setId(2)->setPath('source/file.pdf')->setExt('pdf');

        $this->storage->method('get')->with('source/file.pdf')->willReturn('content');
        $this->storage->expects($this->once())
            ->method('put')
            ->with('target/file.pdf', 'content')
        ;
        $this->storage->expects($this->once())
            ->method('delete')
            ->with('source/file.pdf')
        ;

        $this->service->replace($file, $replaceFile);
    }

    public function test_replace_different_extension_skips_content_copy(): void
    {
        $file        = (new FileEntity)->setId(1)->setPath('target/file.pdf')->setExt('pdf');
        $replaceFile = (new FileEntity)->setId(2)->setPath('source/file.txt')->setExt('txt');

        $this->storage->expects($this->never())->method('put');
        $this->storage->expects($this->once())
            ->method('delete')
            ->with('source/file.txt')
        ;

        $this->service->replace($file, $replaceFile);
    }

    public function test_move_moves_file(): void
    {
        $file = (new FileEntity)->setId(1)->setPath('old/path/file.pdf');

        $this->stringGenerator->method('normalizePath')
            ->willReturnCallback(fn(string $p) => $p)
        ;

        $this->storage->method('get')->with('old/path/file.pdf')->willReturn('content');
        $this->storage->expects($this->once())
            ->method('put')
            ->with('new/path/file.pdf', 'content')
        ;
        $this->storage->expects($this->once())
            ->method('delete')
            ->with('old/path/file.pdf')
        ;

        $savedFile = (new FileEntity)->setId(1)->setPath('new/path/file.pdf');
        $this->fileService->method('save')->willReturn($savedFile);

        $result = $this->service->move($file, 'new/path/file.pdf');

        $this->assertSame('new/path/file.pdf', $result->getPath());
    }

    public function test_store_stores_and_returns_entity(): void
    {
        $uploadedFile = new UploadedFile('test.pdf', '/tmp/test.pdf', 'application/pdf', 1024, 'content');

        $this->stringGenerator->method('normalizePath')
            ->willReturnCallback(fn(string $p) => preg_replace('#/{2,}#', '/', $p))
        ;
        $this->stringGenerator->method('random')->with(8)->willReturn('xyz78901');
        $this->storage->method('exists')->willReturn(false);

        $this->storage->expects($this->once())
            ->method('put')
            ->with('public/dir/xyz78901.pdf', 'content', true)
        ;

        $result = $this->service->store($uploadedFile, 'dir');

        $this->assertSame('test.pdf', $result->getName());
        $this->assertSame('pdf', $result->getExt());
        $this->assertSame('public/dir/xyz78901.pdf', $result->getPath());
    }

    public function test_store_and_save_stores_and_saves_each_file(): void
    {
        $file1 = new UploadedFile('a.pdf', '/tmp/a.pdf', 'application/pdf', 100, 'content1');
        $file2 = new UploadedFile('b.jpg', '/tmp/b.jpg', 'image/jpeg', 200, 'content2');

        $this->stringGenerator->method('normalizePath')
            ->willReturnCallback(fn(string $p) => $p)
        ;
        $this->stringGenerator->method('random')->with(8)
            ->willReturnOnConsecutiveCalls('aaa111', 'bbb222')
        ;
        $this->storage->method('exists')->willReturn(false);
        $this->storage->method('put')->willReturn(true);

        $this->fileService->expects($this->exactly(2))
            ->method('save')
            ->willReturnCallback(fn(FileEntity $f) => $f)
        ;

        $this->service->storeAndSave(
            [$file1, $file2],
            relatedId: 10,
            type     : FileTypeEnum::NEWS,
        );
    }
}
