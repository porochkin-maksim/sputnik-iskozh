<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Payment;

use Core\App\Billing\Payment\LinkPaymentCommand;
use Core\App\Billing\Payment\Validator\LinkPaymentValidator;
use Core\Domains\Billing\Payment\PaymentEntity;
use Core\Domains\Billing\Payment\PaymentService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class LinkPaymentCommandTest extends TestCase
{
    private PaymentService       $paymentService;
    private LinkPaymentValidator $validator;
    private LinkPaymentCommand   $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentService = $this->createMock(PaymentService::class);
        $this->validator      = $this->createMock(LinkPaymentValidator::class);

        $this->command = new LinkPaymentCommand(
            $this->paymentService,
            $this->validator,
        );
    }

    public function test_execute_links_payment(): void
    {
        $this->validator->expects($this->once())->method('validate');

        $payment = new PaymentEntity;
        $payment->setId(1);

        $this->paymentService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($payment)
        ;

        $this->paymentService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(PaymentEntity $p) => $p->isVerified() === true
                                                           && $p->isModerated() === true
                                                           && $p->getName() === 'Test'
                                                           && $p->getCost() === 1000.0
                                                           && $p->getComment() === 'comment'
                                                           && $p->getAccountId() === 10
                                                           && $p->getInvoiceId() === 5,
            ))
            ->willReturnArgument(0)
        ;

        $result = $this->command->execute(1, 'Test', 1000.0, 'comment', 10, 5);

        $this->assertInstanceOf(PaymentEntity::class, $result);
        $this->assertSame(1, $result->getId());
    }

    public function test_execute_returns_null_when_no_id(): void
    {
        $this->validator->expects($this->once())->method('validate');

        $this->paymentService->expects($this->never())->method('getById');
        $this->paymentService->expects($this->never())->method('save');

        $result = $this->command->execute(null, 'Test', 1000.0, 'comment', 10, 5);

        $this->assertNull($result);
    }

    public function test_execute_returns_null_when_payment_not_found(): void
    {
        $this->validator->expects($this->once())->method('validate');

        $this->paymentService->expects($this->once())
            ->method('getById')
            ->with(999)
            ->willReturn(null)
        ;

        $this->paymentService->expects($this->never())->method('save');

        $result = $this->command->execute(999, 'Test', 1000.0, 'comment', 10, 5);

        $this->assertNull($result);
    }

    public function test_execute_throws_on_validation_error(): void
    {
        $this->validator->expects($this->once())
            ->method('validate')
            ->willThrowException(new ValidationException(['cost' => ['error']]))
        ;

        $this->expectException(ValidationException::class);

        $this->command->execute(1, 'Test', null, 'comment', null, 5);
    }
}
