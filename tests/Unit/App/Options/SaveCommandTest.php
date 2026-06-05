<?php declare(strict_types=1);

namespace Tests\Unit\App\Options;

use Core\App\Options\SaveCommand;
use Core\App\Options\SaveValidator;
use Core\Domains\Option\OptionEntity;
use Core\Domains\Option\OptionFactory;
use Core\Domains\Option\OptionService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class SaveCommandTest extends TestCase
{
    private SaveValidator $saveValidator;
    private OptionService $optionService;
    private OptionFactory $optionFactory;
    private SaveCommand   $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->saveValidator = $this->createMock(SaveValidator::class);
        $this->optionService = $this->createMock(OptionService::class);
        $this->optionFactory = $this->createMock(OptionFactory::class);
        $this->command       = new SaveCommand(
            $this->saveValidator,
            $this->optionService,
            $this->optionFactory,
        );
    }

    public function test_execute_saves_option(): void
    {
        $option = (new OptionEntity)->setId(1);

        $this->saveValidator->method('validate');

        $this->optionService->method('getById')->with(1)->willReturn($option);

        $this->optionService->expects($this->once())
            ->method('save')
            ->willReturn($option)
        ;

        $result = $this->command->execute(1, ['key' => 'value']);

        $this->assertSame($option, $result);
    }

    public function test_execute_validates_before_save(): void
    {
        $this->saveValidator->expects($this->once())
            ->method('validate')
            ->willThrowException(new ValidationException(['id' => ['error']]))
        ;

        $this->optionService->expects($this->never())->method('save');

        $this->expectException(ValidationException::class);

        $this->command->execute(999, []);
    }
}
