<?php declare(strict_types=1);

namespace Tests\Unit\App\Files;

use Core\App\Files\DeleteCommand;
use Core\Domains\Files\FileEntity;
use Core\Domains\Files\FileService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class DeleteCommandTest extends TestCase
{
    private FileService   $fileService;
    private DeleteCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileService = $this->createMock(FileService::class);
        $this->command     = new DeleteCommand($this->fileService);
    }

    public function test_execute_deletes_file(): void
    {
        $this->fileService->method('getById')->with(1)->willReturn(new FileEntity);

        $this->fileService->expects($this->once())
            ->method('deleteById')
            ->with(1)
            ->willReturn(true)
        ;

        $result = $this->command->execute(1);

        $this->assertTrue($result);
    }

    public function test_execute_throws_when_file_not_found(): void
    {
        $this->fileService->method('getById')->willReturn(null);

        $this->fileService->expects($this->never())->method('deleteById');

        $this->expectException(ValidationException::class);

        $this->command->execute(999);
    }
}
