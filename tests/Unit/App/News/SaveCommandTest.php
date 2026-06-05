<?php declare(strict_types=1);

namespace Tests\Unit\App\News;

use Core\App\News\SaveCommand;
use Core\App\News\SaveValidator;
use Core\Domains\News\NewsEntity;
use Core\Domains\News\NewsFactory;
use Core\Domains\News\NewsService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class SaveCommandTest extends TestCase
{
    private NewsService   $newsService;
    private NewsFactory   $newsFactory;
    private SaveValidator $saveValidator;
    private SaveCommand   $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->newsService   = $this->createMock(NewsService::class);
        $this->newsFactory   = $this->createMock(NewsFactory::class);
        $this->saveValidator = $this->createMock(SaveValidator::class);
        $this->command       = new SaveCommand(
            $this->newsService,
            $this->newsFactory,
            $this->saveValidator,
        );
    }

    public function test_execute_saves_news(): void
    {
        $default = new NewsEntity;
        $saved   = (new NewsEntity)->setId(1);

        $this->saveValidator->method('validate');

        $this->newsFactory->method('makeDefault')->willReturn($default);

        $this->newsService->expects($this->once())
            ->method('save')
            ->willReturn($saved)
        ;

        $result = $this->command->execute(null, 'Title', 'Desc', 'Article', 1, false, null);

        $this->assertSame($saved, $result);
    }

    public function test_execute_updates_existing_news(): void
    {
        $default = new NewsEntity;
        $saved   = (new NewsEntity)->setId(5);

        $this->saveValidator->method('validate');

        $this->newsFactory->method('makeDefault')->willReturn($default);

        $this->newsService->method('save')->willReturn($saved);

        $result = $this->command->execute(5, 'Updated', null, null, 0, true, '2024-01-01');

        $this->assertSame($saved, $result);
    }

    public function test_execute_validates_before_save(): void
    {
        $this->saveValidator->expects($this->once())
            ->method('validate')
            ->willThrowException(new ValidationException(['title' => ['error']]))
        ;

        $this->newsService->expects($this->never())->method('save');

        $this->expectException(ValidationException::class);

        $this->command->execute(null, '', null, null, null, false, null);
    }
}
