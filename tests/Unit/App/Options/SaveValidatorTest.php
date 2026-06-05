<?php declare(strict_types=1);

namespace Tests\Unit\App\Options;

use Core\App\Options\SaveValidator;
use Core\Domains\Option\OptionEntity;
use Core\Domains\Option\OptionService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class SaveValidatorTest extends TestCase
{
    private OptionService $optionService;
    private SaveValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->optionService = $this->createMock(OptionService::class);
        $this->validator     = new SaveValidator($this->optionService);
    }

    public function test_valid_data_passes(): void
    {
        $this->optionService->method('getById')->with(1)->willReturn(
            new OptionEntity,
        );

        $this->validator->validate(1, ['key' => 'value']);
    }

    public function test_invalid_option_id_throws(): void
    {
        $this->expectException(ValidationException::class);

        $this->validator->validate(999, []);
    }

    public function test_validation_returns_all_errors(): void
    {
        try {
            $this->validator->validate(999, []);
        }
        catch (ValidationException $e) {
            $this->assertArrayHasKey('id', $e->errors);
        }
    }
}
