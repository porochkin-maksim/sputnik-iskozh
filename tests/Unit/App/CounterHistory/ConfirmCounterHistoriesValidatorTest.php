<?php declare(strict_types=1);

namespace Tests\Unit\App\CounterHistory;

use Core\App\CounterHistory\ConfirmCounterHistoriesValidator;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class ConfirmCounterHistoriesValidatorTest extends TestCase
{
    private ConfirmCounterHistoriesValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new ConfirmCounterHistoriesValidator;
    }

    public function test_valid_ids_passes(): void
    {
        $this->validator->validate([1, 2, 3]);
        $this->expectNotToPerformAssertions();
    }

    public function test_empty_ids_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate([]);
    }
}
