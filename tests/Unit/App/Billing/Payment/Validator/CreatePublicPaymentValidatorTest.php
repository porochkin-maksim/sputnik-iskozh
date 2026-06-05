<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Payment\Validator;

use Core\App\Billing\Payment\Validator\CreatePublicPaymentValidator;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class CreatePublicPaymentValidatorTest extends TestCase
{
    private CreatePublicPaymentValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new CreatePublicPaymentValidator;
    }

    public function test_valid_data_passes(): void
    {
        $this->validator->validate('text', 100.0);

        $this->expectNotToPerformAssertions();
    }

    public function test_valid_zero_cost_passes(): void
    {
        $this->validator->validate('text', 0.0);

        $this->expectNotToPerformAssertions();
    }

    public function test_empty_text_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate('', 100.0);
    }

    public function test_whitespace_text_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate('   ', 100.0);
    }

    public function test_negative_cost_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate('text', -1.0);
    }

    public function test_validation_returns_all_errors(): void
    {
        try {
            $this->validator->validate('', -5.0);
            $this->fail('Expected ValidationException');
        }
        catch (ValidationException $e) {
            $this->assertArrayHasKey('text', $e->errors);
            $this->assertArrayHasKey('cost', $e->errors);
        }
    }
}
