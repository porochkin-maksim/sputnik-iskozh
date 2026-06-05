<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Claim;

use Core\App\Billing\Claim\SaveCommand;
use Core\App\Billing\Claim\SaveValidator;
use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\Billing\Claim\ClaimFactory;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class SaveCommandTest extends TestCase
{
    private ClaimService  $claimService;
    private ClaimFactory  $claimFactory;
    private SaveValidator $validator;
    private SaveCommand   $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->claimService = $this->createMock(ClaimService::class);
        $this->claimFactory = new ClaimFactory;
        $this->validator    = $this->createMock(SaveValidator::class);

        $this->command = new SaveCommand(
            $this->claimService,
            $this->claimFactory,
            $this->validator,
        );
    }

    public function test_execute_creates_new_claim(): void
    {
        $this->validator->expects($this->once())->method('validate');

        $this->claimService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(ClaimEntity $c) => $c->getInvoiceId() === 1
                                                         && $c->getServiceId() === 2
                                                         && $c->getTariff() === 100.0
                                                         && $c->getCost() === 500.0
                                                         && $c->getName() === 'test',
            ))
            ->willReturnCallback(fn(ClaimEntity $c) => $c->setId(10))
        ;

        $result = $this->command->execute(null, 1, 2, 100.0, 500.0, 'test');

        $this->assertSame(10, $result->getId());
    }

    public function test_execute_updates_existing_claim(): void
    {
        $existing = new ClaimEntity;
        $existing->setId(5)->setInvoiceId(1)->setServiceId(2);

        $this->validator->expects($this->once())->method('validate');

        $this->claimService->expects($this->once())
            ->method('getById')
            ->with(5)
            ->willReturn($existing)
        ;

        $this->claimService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(ClaimEntity $c) => $c->getCost() === 600.0
                                                         && $c->getName() === 'updated',
            ))
            ->willReturnCallback(fn(ClaimEntity $c) => $c)
        ;

        $result = $this->command->execute(5, 1, 2, 200.0, 600.0, 'updated');

        $this->assertSame(5, $result->getId());
        $this->assertSame(600.0, $result->getCost());
    }

    public function test_execute_returns_null_when_claim_not_found(): void
    {
        $this->validator->expects($this->once())->method('validate');

        $this->claimService->expects($this->once())
            ->method('getById')
            ->with(999)
            ->willReturn(null)
        ;

        $this->claimService->expects($this->never())->method('save');

        $result = $this->command->execute(999, 1, 2, 100.0, 500.0, 'test');

        $this->assertNull($result);
    }

    public function test_execute_throws_on_validation_error(): void
    {
        $this->validator->expects($this->once())
            ->method('validate')
            ->willThrowException(new ValidationException(['cost' => ['error']]))
        ;

        $this->expectException(ValidationException::class);

        $this->command->execute(null, 1, 2, 100.0, null, 'test');
    }

    public function test_execute_sets_tariff_from_cost_when_tariff_null(): void
    {
        $this->validator->expects($this->once())->method('validate');

        $this->claimService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(ClaimEntity $c) => $c->getTariff() === 500.0
                                                         && $c->getCost() === 500.0,
            ))
            ->willReturnCallback(fn(ClaimEntity $c) => $c->setId(1))
        ;

        $this->command->execute(null, 1, 2, null, 500.0, 'test');
    }
}
