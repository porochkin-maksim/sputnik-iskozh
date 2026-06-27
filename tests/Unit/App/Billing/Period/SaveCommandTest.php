<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Period;

use Carbon\Carbon;
use Core\App\Billing\Period\SaveCommand;
use Core\App\Billing\Period\SaveValidator;
use Core\Domains\Billing\Period\PeriodEntity;
use Core\Domains\Billing\Period\PeriodFactory;
use Core\Domains\Billing\Period\PeriodService;
use Core\Exceptions\ValidationException;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class SaveCommandTest extends TestCase
{
    private PeriodFactory $periodFactory;
    private PeriodService $periodService;
    private SaveValidator $validator;
    private SaveCommand   $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->periodFactory = $this->createMock(PeriodFactory::class);
        $this->periodService = $this->createMock(PeriodService::class);
        $this->validator     = $this->createMock(SaveValidator::class);

        $this->command = new SaveCommand(
            $this->periodFactory,
            $this->periodService,
            $this->validator,
        );
    }

    public function test_execute_creates_new_period(): void
    {
        Bus::fake();
        $name    = '2025';
        $startAt = Carbon::parse('2025-01-01');
        $endAt   = Carbon::parse('2025-12-31');
        $default = new PeriodEntity;

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($name, $startAt, $endAt)
        ;

        $this->periodFactory->expects($this->once())
            ->method('makeDefault')
            ->willReturn($default)
        ;

        $saved = (new PeriodEntity)->setId(1)->setName($name)->setStartAt($startAt)->setEndAt($endAt)->setIsClosed(true);

        $this->periodService->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(PeriodEntity::class))
            ->willReturn($saved)
        ;

        $result = $this->command->execute(null, $name, $startAt, $endAt, true);

        $this->assertSame($saved, $result);
    }

    public function test_execute_updates_existing_period(): void
    {
        $id       = 1;
        $name     = '2025';
        $startAt  = Carbon::parse('2025-01-01');
        $endAt    = Carbon::parse('2025-12-31');
        $existing = (new PeriodEntity)->setId($id);

        $this->validator->expects($this->once())
            ->method('validate')
            ->with($name, $startAt, $endAt)
        ;

        $this->periodService->expects($this->once())
            ->method('getById')
            ->with($id)
            ->willReturn($existing)
        ;

        $saved = (new PeriodEntity)->setId($id)->setName($name)->setStartAt($startAt)->setEndAt($endAt)->setIsClosed(false);

        $this->periodService->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(PeriodEntity::class))
            ->willReturn($saved)
        ;

        $result = $this->command->execute($id, $name, $startAt, $endAt, false);

        $this->assertSame($saved, $result);
    }

    public function test_execute_returns_null_when_not_found(): void
    {
        $this->validator->expects($this->once())->method('validate');

        $this->periodService->expects($this->once())
            ->method('getById')
            ->with(999)
            ->willReturn(null)
        ;

        $result = $this->command->execute(999, '2025', Carbon::parse('2025-01-01'), Carbon::parse('2025-12-31'), false);

        $this->assertNull($result);
    }

    public function test_execute_throws_when_validator_fails(): void
    {
        $this->validator->expects($this->once())
            ->method('validate')
            ->willThrowException(new ValidationException(['name' => ['error']]))
        ;

        $this->expectException(ValidationException::class);
        $this->command->execute(null, null, null, null, false);
    }
}
