<?php declare(strict_types=1);

namespace Tests\Unit\App\Files;

use Core\App\Files\DownCommand;
use Core\App\Files\FileOrderingService;
use Core\Domains\Files\FileEntity;
use Core\Domains\Files\FileService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class DownCommandTest extends TestCase
{
    private FileService         $fileService;
    private FileOrderingService $fileOrderingService;
    private DownCommand         $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileService         = $this->createMock(FileService::class);
        $this->fileOrderingService = $this->createMock(FileOrderingService::class);
        $this->command             = new DownCommand(
            $this->fileService,
            $this->fileOrderingService,
        );
    }

    public function test_execute_moves_down(): void
    {
        $file = (new FileEntity)->setId(1);

        $this->fileService->method('getById')->with(1)->willReturn($file);

        $this->fileOrderingService->expects($this->once())
            ->method('saveFileOrderIndex')
            ->with($file, 3)
        ;

        $result = $this->command->execute(1, 2);

        $this->assertTrue($result);
    }

    public function test_execute_throws_when_file_not_found(): void
    {
        $this->fileService->method('getById')->willReturn(null);

        $this->fileOrderingService->expects($this->never())->method('saveFileOrderIndex');

        $this->expectException(ValidationException::class);

        $this->command->execute(999, 2);
    }
}
