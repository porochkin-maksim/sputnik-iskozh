<?php declare(strict_types=1);

namespace Tests\Unit\App\News;

use Core\App\News\GetListCommand;
use Core\App\News\GetListValidator;
use Core\Domains\News\NewsCategoryEnum;
use Core\Domains\News\NewsSearchResponse;
use Core\Domains\News\NewsService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class GetListCommandTest extends TestCase
{
    private NewsService      $newsService;
    private GetListValidator $listValidator;
    private GetListCommand   $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->newsService   = $this->createMock(NewsService::class);
        $this->listValidator = $this->createMock(GetListValidator::class);
        $this->command       = new GetListCommand($this->newsService, $this->listValidator);
    }

    public function test_execute_returns_news(): void
    {
        $response = new NewsSearchResponse;

        $this->listValidator->method('validate');

        $this->newsService->expects($this->once())
            ->method('search')
            ->willReturn($response)
        ;

        $result = $this->command->execute(10, 0, null, null, false, null, true);

        $this->assertSame($response, $result);
    }

    public function test_execute_with_category_filter(): void
    {
        $response = new NewsSearchResponse;

        $this->listValidator->method('validate');

        $this->newsService->method('search')->willReturn($response);

        $result = $this->command->execute(10, 0, null, NewsCategoryEnum::ANNOUNCEMENT, false, null, true);

        $this->assertSame($response, $result);
    }

    public function test_execute_with_files_and_locked(): void
    {
        $response = new NewsSearchResponse;

        $this->listValidator->method('validate');

        $this->newsService->method('search')->willReturn($response);

        $result = $this->command->execute(10, 0, null, null, true, false, false);

        $this->assertSame($response, $result);
    }

    public function test_execute_validates_before_search(): void
    {
        $this->listValidator->expects($this->once())
            ->method('validate')
            ->willThrowException(new ValidationException(['limit' => ['error']]))
        ;

        $this->newsService->expects($this->never())->method('search');

        $this->expectException(ValidationException::class);

        $this->command->execute(0, null, null, null, false, null, true);
    }
}
