<?php declare(strict_types=1);

namespace Tests\Unit\App\Files;

use Core\App\Files\SaveCommand;
use Core\App\Files\SaveValidator;
use Core\Domains\Files\FileEntity;
use Core\Domains\Files\FileService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class SaveCommandTest extends TestCase
{
    private FileService   $fileService;
    private SaveValidator $saveValidator;
    private SaveCommand   $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileService   = $this->createMock(FileService::class);
        $this->saveValidator = $this->createMock(SaveValidator::class);
        $this->command       = new SaveCommand($this->fileService, $this->saveValidator);
    }

    public function test_execute_renames_file(): void
    {
        $file = (new FileEntity)->setId(1)->setName('old.pdf');

        $this->saveValidator->method('validate');

        $this->fileService->method('getById')->with(1)->willReturn($file);

        $this->fileService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(FileEntity $f) => $f->getName() === 'new-name.pdf'))
        ;

        $result = $this->command->execute(1, 'new-name.pdf');

        $this->assertTrue($result);
    }

    public function test_execute_throws_when_file_not_found(): void
    {
        $this->saveValidator->method('validate');

        $this->fileService->method('getById')->willReturn(null);

        $this->expectException(ValidationException::class);

        $this->command->execute(999, 'new.pdf');
    }

    public function test_execute_validates_before_search(): void
    {
        $this->saveValidator->expects($this->once())
            ->method('validate')
            ->willThrowException(new ValidationException(['name' => ['error']]))
        ;

        $this->fileService->expects($this->never())->method('getById');

        $this->expectException(ValidationException::class);

        $this->command->execute(1, '');
    }
}
