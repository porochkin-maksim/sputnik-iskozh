<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Service;

use Carbon\Carbon;
use Core\App\Billing\Service\SaveValidator;
use Core\Domains\Billing\Period\PeriodEntity;
use Core\Domains\Billing\Period\PeriodService;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class SaveValidatorTest extends TestCase
{
    private PeriodService $periodService;
    private SaveValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->periodService = $this->createMock(PeriodService::class);

        $this->validator = new SaveValidator(
            $this->periodService,
        );
    }

    public function test_valid_data_passes(): void
    {
        $this->periodService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn(new PeriodEntity)
        ;

        $this->validator->validate(1, ServiceTypeEnum::MEMBERSHIP_FEE, 'Членский взнос', 1000.0);
    }

    public function test_null_period_id_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate(null, ServiceTypeEnum::MEMBERSHIP_FEE, 'test', 100.0);
    }

    public function test_period_not_found_throws(): void
    {
        $this->periodService->expects($this->once())
            ->method('getById')
            ->with(999)
            ->willReturn(null)
        ;

        $this->expectException(ValidationException::class);
        $this->validator->validate(999, ServiceTypeEnum::MEMBERSHIP_FEE, 'test', 100.0);
    }

    public function test_null_type_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate(1, null, 'test', 100.0);
    }

    public function test_null_name_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate(1, ServiceTypeEnum::MEMBERSHIP_FEE, null, 100.0);
    }

    public function test_empty_name_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate(1, ServiceTypeEnum::MEMBERSHIP_FEE, '', 100.0);
    }

    public function test_null_cost_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate(1, ServiceTypeEnum::MEMBERSHIP_FEE, 'test', null);
    }

    public function test_negative_cost_throws(): void
    {
        $this->expectException(ValidationException::class);
        $this->validator->validate(1, ServiceTypeEnum::MEMBERSHIP_FEE, 'test', -1.0);
    }

    public function test_validation_returns_all_errors(): void
    {
        try {
            $this->validator->validate(null, null, null, null);
            $this->fail('Expected ValidationException');
        }
        catch (ValidationException $e) {
            $this->assertArrayHasKey('period_id', $e->errors);
            $this->assertArrayHasKey('type', $e->errors);
            $this->assertArrayHasKey('name', $e->errors);
            $this->assertArrayHasKey('cost', $e->errors);
        }
    }

    public function test_period_from_after_period_to_throws(): void
    {
        $this->periodService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn(new PeriodEntity)
        ;

        $this->expectException(ValidationException::class);

        $this->validator->validate(
            1,
            ServiceTypeEnum::MEMBERSHIP_FEE,
            'test',
            100.0,
            Carbon::parse('2026-06-01'),
            Carbon::parse('2026-05-01'),
        );
    }

    public function test_period_from_before_period_to_passes(): void
    {
        $this->periodService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn(new PeriodEntity)
        ;

        $this->validator->validate(
            1,
            ServiceTypeEnum::MEMBERSHIP_FEE,
            'test',
            100.0,
            Carbon::parse('2026-01-01'),
            Carbon::parse('2026-12-31'),
        );
    }

    public function test_period_from_equal_period_to_passes(): void
    {
        $this->periodService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn(new PeriodEntity)
        ;

        $this->validator->validate(
            1,
            ServiceTypeEnum::MEMBERSHIP_FEE,
            'test',
            100.0,
            Carbon::parse('2026-06-01'),
            Carbon::parse('2026-06-01'),
        );
    }

    public function test_period_dates_both_null_passes(): void
    {
        $this->periodService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn(new PeriodEntity)
        ;

        $this->validator->validate(
            1,
            ServiceTypeEnum::MEMBERSHIP_FEE,
            'test',
            100.0,
            null,
            null,
        );
    }

    public function test_period_from_null_period_to_not_null_passes(): void
    {
        $this->periodService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn(new PeriodEntity)
        ;

        $this->validator->validate(
            1,
            ServiceTypeEnum::MEMBERSHIP_FEE,
            'test',
            100.0,
            null,
            Carbon::parse('2026-12-31'),
        );
    }
}
