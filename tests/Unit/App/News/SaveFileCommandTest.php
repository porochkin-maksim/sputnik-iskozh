<?php declare(strict_types=1);

namespace Tests\Unit\App\News;

use Core\App\News\SaveFileCommand;
use Core\App\News\SaveFileValidator;
use Core\Domains\Files\FileEntity;
use Core\Domains\Files\FileTypeEnum;
use Core\Domains\News\FileService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class SaveFileCommandTest extends TestCase
{
    private FileService       $fileService;
    private SaveFileValidator $saveFileValidator;
    private SaveFileCommand   $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileService       = $this->createMock(FileService::class);
        $this->saveFileValidator = $this->createMock(SaveFileValidator::class);
        $this->command           = new SaveFileCommand(
            $this->fileService,
            $this->saveFileValidator,
        );
    }

    public function test_execute_renames_news_file(): void
    {
        $file = (new FileEntity)->setId(1)->setType(FileTypeEnum::NEWS);

        $this->saveFileValidator->method('validate');

        $this->fileService->method('getById')->with(1)->willReturn($file);

        $this->fileService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(FileEntity $f) => $f->getName() === 'new-name.pdf'))
        ;

        $result = $this->command->execute(1, 'new-name.pdf');

        $this->assertTrue($result);
    }

    public function test_execute_returns_false_when_file_not_found(): void
    {
        $this->saveFileValidator->method('validate');

        $this->fileService->method('getById')->willReturn(null);

        $this->fileService->expects($this->never())->method('save');

        $result = $this->command->execute(999, 'new.pdf');

        $this->assertFalse($result);
    }

    public function test_execute_returns_false_wrong_type(): void
    {
        $file = (new FileEntity)->setId(1)->setType(FileTypeEnum::TICKET);

        $this->saveFileValidator->method('validate');

        $this->fileService->method('getById')->willReturn($file);

        $this->fileService->expects($this->never())->method('save');

        $result = $this->command->execute(1, 'new.pdf');

        $this->assertFalse($result);
    }

    public function test_execute_validates_before_search(): void
    {
        $this->saveFileValidator->expects($this->once())
            ->method('validate')
            ->willThrowException(new ValidationException(['name' => ['error']]))
        ;

        $this->fileService->expects($this->never())->method('getById');

        $this->expectException(ValidationException::class);

        $this->command->execute(1, '');
    }
}
