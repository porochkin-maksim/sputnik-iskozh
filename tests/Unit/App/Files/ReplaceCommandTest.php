<?php declare(strict_types=1);

namespace Tests\Unit\App\Files;

use Core\App\Files\FileTransferService;
use Core\App\Files\ReplaceCommand;
use Core\Domains\Files\FileEntity;
use Core\Domains\Files\FileService;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class ReplaceCommandTest extends TestCase
{
    private FileService         $fileService;
    private FileTransferService $fileTransferService;
    private ReplaceCommand      $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileService         = $this->createMock(FileService::class);
        $this->fileTransferService = $this->createMock(FileTransferService::class);
        $this->command             = new ReplaceCommand(
            $this->fileService,
            $this->fileTransferService,
        );
    }

    public function test_execute_replaces_file(): void
    {
        $file     = (new FileEntity)->setId(1);
        $uploaded = new UploadedFile('new.pdf', '/tmp/new.pdf', 'pdf', 100, 'content');
        $newFile  = (new FileEntity)->setId(null);

        $this->fileService->method('getById')->with(1)->willReturn($file);

        $this->fileTransferService->expects($this->once())
            ->method('store')
            ->with($uploaded, 'uploads')
            ->willReturn($newFile)
        ;

        $this->fileTransferService->expects($this->once())
            ->method('replace')
            ->with($file, $newFile)
        ;

        $result = $this->command->execute(1, $uploaded, 'uploads');

        $this->assertTrue($result);
    }

    public function test_execute_throws_when_replacing_file_not_found(): void
    {
        $this->fileService->method('getById')->willReturn(null);

        $this->expectException(ValidationException::class);

        $this->command->execute(999, new UploadedFile('n.pdf', '/n.pdf', 'pdf', 1, ''), 'dir');
    }

    public function test_execute_throws_when_uploaded_file_missing(): void
    {
        $this->fileService->method('getById')->willReturn(new FileEntity);

        $this->expectException(ValidationException::class);

        $this->command->execute(1, null, 'dir');
    }

    public function test_execute_returns_all_errors(): void
    {
        $this->fileService->method('getById')->willReturn(null);

        try {
            $this->command->execute(999, null, 'dir');
        }
        catch (ValidationException $e) {
            $this->assertArrayHasKey('replace_file', $e->errors);
            $this->assertArrayHasKey('upload_file', $e->errors);
        }
    }
}
