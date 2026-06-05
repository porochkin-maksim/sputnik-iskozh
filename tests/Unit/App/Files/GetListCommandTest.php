<?php declare(strict_types=1);

namespace Tests\Unit\App\Files;

use Core\App\Files\GetListCommand;
use Core\App\Files\GetListValidator;
use Core\Domains\Files\FileSearchResponse;
use Core\Domains\Files\FileService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class GetListCommandTest extends TestCase
{
    private FileService      $fileService;
    private GetListValidator $listValidator;
    private GetListCommand   $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileService   = $this->createMock(FileService::class);
        $this->listValidator = $this->createMock(GetListValidator::class);
        $this->command       = new GetListCommand($this->fileService, $this->listValidator);
    }

    public function test_execute_with_parent_id(): void
    {
        $response = new FileSearchResponse;

        $this->listValidator->method('validate');

        $this->fileService->expects($this->once())
            ->method('search')
            ->willReturn($response)
        ;

        $result = $this->command->execute(10, 5, null, false);

        $this->assertSame($response, $result);
    }

    public function test_execute_without_parent_id(): void
    {
        $response = new FileSearchResponse;

        $this->listValidator->method('validate');

        $this->fileService->expects($this->once())
            ->method('search')
            ->willReturn($response)
        ;

        $result = $this->command->execute(10, null, null, false);

        $this->assertSame($response, $result);
    }

    public function test_execute_with_sorting(): void
    {
        $response = new FileSearchResponse;

        $this->listValidator->method('validate');

        $this->fileService->expects($this->once())
            ->method('search')
            ->willReturn($response)
        ;

        $result = $this->command->execute(10, null, 'name', true);

        $this->assertSame($response, $result);
    }

    public function test_execute_validates_before_search(): void
    {
        $this->listValidator->expects($this->once())
            ->method('validate')
            ->willThrowException(new ValidationException(['limit' => ['error']]))
        ;

        $this->fileService->expects($this->never())->method('search');

        $this->expectException(ValidationException::class);

        $this->command->execute(0, null, null, false);
    }
}
