<?php declare(strict_types=1);

namespace Tests\Unit\App\Counter\Validator;

use Core\App\Counter\Validator\CreateProfileCounterValidator;
use Core\Domains\Counter\CounterCollection;
use Core\Domains\Counter\CounterEntity;
use Core\Domains\Counter\CounterSearchResponse;
use Core\Domains\Counter\CounterService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class CreateProfileCounterValidatorTest extends TestCase
{
    private CounterService                $counterService;
    private CreateProfileCounterValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->counterService = $this->createMock(CounterService::class);
        $this->validator      = new CreateProfileCounterValidator($this->counterService);
    }

    public function test_valid_data_passes(): void
    {
        $response = new CounterSearchResponse;
        $response->setItems(new CounterCollection);
        $this->counterService->method('search')->willReturn($response);

        $this->validator->validate('12345', 100, 'fake-file');

        $this->expectNotToPerformAssertions();
    }

    public function test_empty_number_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate('', 100, 'fake-file');
    }

    public function test_null_number_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate(null, 100, 'fake-file');
    }

    public function test_null_value_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate('12345', null, 'fake-file');
    }

    public function test_null_file_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate('12345', 100, null);
    }

    public function test_duplicate_number_throws(): void
    {
        $existing = new CounterEntity;
        $existing->setId(1)->setNumber('12345');

        $response = new CounterSearchResponse;
        $response->setItems(new CounterCollection([$existing]));

        $this->counterService->method('search')->willReturn($response);

        $this->expectException(ValidationException::class);
        $this->validator->validate('12345', 100, 'fake-file');
    }

    public function test_validation_returns_all_errors(): void
    {
        try {
            $this->validator->validate(null, null, null);
            $this->fail('Expected ValidationException');
        }
        catch (ValidationException $e) {
            $this->assertArrayHasKey('number', $e->errors);
            $this->assertArrayHasKey('value', $e->errors);
            $this->assertArrayHasKey('file', $e->errors);
        }
    }
}
