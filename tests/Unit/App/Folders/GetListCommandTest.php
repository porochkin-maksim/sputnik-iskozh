<?php declare(strict_types=1);

namespace Tests\Unit\App\Folders;

use Core\App\Folders\GetListCommand;
use Core\App\Folders\GetListValidator;
use Core\Domains\Folders\FolderSearchResponse;
use Core\Domains\Folders\FolderService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class GetListCommandTest extends TestCase
{
    private FolderService    $folderService;
    private GetListValidator $listValidator;
    private GetListCommand   $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->folderService = $this->createMock(FolderService::class);
        $this->listValidator = $this->createMock(GetListValidator::class);
        $this->command       = new GetListCommand($this->folderService, $this->listValidator);
    }

    public function test_execute_with_parent_id(): void
    {
        $response = new FolderSearchResponse;

        $this->listValidator->method('validate');

        $this->folderService->expects($this->once())
            ->method('search')
            ->willReturn($response)
        ;

        $result = $this->command->execute(10, 5);

        $this->assertSame($response, $result);
    }

    public function test_execute_without_parent_id(): void
    {
        $response = new FolderSearchResponse;

        $this->listValidator->method('validate');

        $this->folderService->expects($this->once())
            ->method('search')
            ->willReturn($response)
        ;

        $result = $this->command->execute(10, null);

        $this->assertSame($response, $result);
    }

    public function test_execute_validates_before_search(): void
    {
        $this->listValidator->expects($this->once())
            ->method('validate')
            ->willThrowException(new ValidationException(['limit' => ['error']]))
        ;

        $this->folderService->expects($this->never())->method('search');

        $this->expectException(ValidationException::class);

        $this->command->execute(0, null);
    }
}
