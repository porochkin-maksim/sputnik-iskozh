<?php declare(strict_types=1);

namespace Tests\Unit\App\Folders;

use Core\App\Folders\SaveCommand;
use Core\App\Folders\SaveValidator;
use Core\Domains\Folders\FolderEntity;
use Core\Domains\Folders\FolderService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class SaveCommandTest extends TestCase
{
    private FolderService $folderService;
    private SaveValidator $saveValidator;
    private SaveCommand   $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->folderService = $this->createMock(FolderService::class);
        $this->saveValidator = $this->createMock(SaveValidator::class);
        $this->command       = new SaveCommand($this->folderService, $this->saveValidator);
    }

    public function test_execute_creates_folder(): void
    {
        $saved = (new FolderEntity)->setId(1)->setName('Docs');

        $this->saveValidator->method('validate');

        $this->folderService->expects($this->once())
            ->method('save')
            ->willReturn($saved)
        ;

        $result = $this->command->execute(null, 'Docs', null);

        $this->assertSame($saved, $result);
    }

    public function test_execute_updates_folder(): void
    {
        $existing = (new FolderEntity)->setId(1);
        $saved    = (new FolderEntity)->setId(1)->setName('Renamed');

        $this->saveValidator->method('validate');

        $this->folderService->method('getById')->with(1)->willReturn($existing);

        $this->folderService->expects($this->once())
            ->method('save')
            ->willReturn($saved)
        ;

        $result = $this->command->execute(1, 'Renamed', null);

        $this->assertSame($saved, $result);
    }

    public function test_execute_throws_when_folder_not_found(): void
    {
        $this->saveValidator->method('validate');

        $this->folderService->method('getById')->willReturn(null);

        $this->expectException(ValidationException::class);

        $this->command->execute(999, 'Ghost', null);
    }

    public function test_execute_throws_when_parent_not_found(): void
    {
        $this->saveValidator->method('validate');

        $this->folderService->method('getById')->willReturnCallback(fn(?int $id) => match ($id) {
            1       => new FolderEntity,
            default => null,
        });

        $this->expectException(ValidationException::class);

        $this->command->execute(1, 'Child', 999);
    }

    public function test_execute_throws_when_folder_is_own_parent(): void
    {
        $this->saveValidator->method('validate');

        $this->folderService->method('getById')->willReturn(new FolderEntity);

        $this->expectException(ValidationException::class);

        $this->command->execute(1, 'Self Parent', 1);
    }

    public function test_execute_validates_before_any_action(): void
    {
        $this->saveValidator->expects($this->once())
            ->method('validate')
            ->willThrowException(new ValidationException(['name' => ['error']]))
        ;

        $this->folderService->expects($this->never())->method('getById');

        $this->expectException(ValidationException::class);

        $this->command->execute(null, '', null);
    }
}
