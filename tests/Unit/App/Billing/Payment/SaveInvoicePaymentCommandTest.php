<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Payment;

use Core\App\Billing\Payment\SaveInvoicePaymentCommand;
use Core\App\Billing\Payment\Validator\LinkPaymentValidator;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Payment\PaymentEntity;
use Core\Domains\Billing\Payment\PaymentFactory;
use Core\Domains\Billing\Payment\PaymentFileService;
use Core\Domains\Billing\Payment\PaymentService;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class SaveInvoicePaymentCommandTest extends TestCase
{
    private PaymentFactory            $paymentFactory;
    private PaymentService            $paymentService;
    private InvoiceService            $invoiceService;
    private PaymentFileService        $fileService;
    private LinkPaymentValidator      $validator;
    private SaveInvoicePaymentCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentFactory = new PaymentFactory;
        $this->paymentService = $this->createMock(PaymentService::class);
        $this->invoiceService = $this->createMock(InvoiceService::class);
        $this->fileService    = $this->createMock(PaymentFileService::class);
        $this->validator      = $this->createMock(LinkPaymentValidator::class);

        $this->command = new SaveInvoicePaymentCommand(
            $this->paymentFactory,
            $this->paymentService,
            $this->invoiceService,
            $this->fileService,
            $this->validator,
        );
    }

    public function test_execute_creates_new_payment(): void
    {
        $invoice = new InvoiceEntity;
        $invoice->setId(5)->setAccountId(10);

        $this->invoiceService->expects($this->once())
            ->method('getById')
            ->with(5)
            ->willReturn($invoice)
        ;

        $this->validator->expects($this->once())
            ->method('validate')
            ->with(1500.0, 10)
        ;

        $this->paymentService->expects($this->never())->method('getById');

        $this->paymentService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(PaymentEntity $p) => $p->getInvoiceId() === 5
                                                           && $p->getAccountId() === 10
                                                           && $p->getCost() === 1500.0
                                                           && $p->getName() === 'Payment'
                                                           && $p->getComment() === 'comment'
                                                           && $p->isModerated() === true
                                                           && $p->isVerified() === true,
            ))
            ->willReturnCallback(fn(PaymentEntity $p) => $p->setId(1))
        ;

        $this->fileService->expects($this->once())
            ->method('storePaymentFiles')
            ->with([], 1)
        ;

        $result = $this->command->execute(5, null, 1500.0, 'Payment', 'comment', '2025-01-15', []);

        $this->assertInstanceOf(PaymentEntity::class, $result);
    }

    public function test_execute_updates_existing_payment(): void
    {
        $invoice = new InvoiceEntity;
        $invoice->setId(5)->setAccountId(10);

        $existingPayment = new PaymentEntity;
        $existingPayment->setId(1);

        $this->invoiceService->expects($this->once())
            ->method('getById')
            ->with(5)
            ->willReturn($invoice)
        ;

        $this->validator->expects($this->once())
            ->method('validate')
            ->with(2000.0, 10)
        ;

        $this->paymentService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($existingPayment)
        ;

        $this->paymentService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(PaymentEntity $p) => $p->getId() === 1
                                                           && $p->getCost() === 2000.0
                                                           && $p->getName() === 'Updated'
                                                           && $p->getComment() === 'new comment',
            ))
            ->willReturnCallback(fn(PaymentEntity $p) => $p->setId(1))
        ;

        $this->fileService->expects($this->once())
            ->method('storePaymentFiles')
            ->with([], 1)
        ;

        $result = $this->command->execute(5, 1, 2000.0, 'Updated', 'new comment', null, []);

        $this->assertInstanceOf(PaymentEntity::class, $result);
    }

    public function test_execute_returns_null_when_invoice_not_found(): void
    {
        $this->invoiceService->expects($this->once())
            ->method('getById')
            ->with(999)
            ->willReturn(null)
        ;

        $this->validator->expects($this->never())->method('validate');
        $this->paymentService->expects($this->never())->method('save');

        $result = $this->command->execute(999, null, 100.0, 'Test', null, null, []);

        $this->assertNull($result);
    }

    public function test_execute_returns_null_when_payment_not_found(): void
    {
        $invoice = new InvoiceEntity;
        $invoice->setId(5)->setAccountId(10);

        $this->invoiceService->expects($this->once())
            ->method('getById')
            ->with(5)
            ->willReturn($invoice)
        ;

        $this->validator->expects($this->once())->method('validate');

        $this->paymentService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn(null)
        ;

        $this->paymentService->expects($this->never())->method('save');

        $result = $this->command->execute(5, 1, 100.0, 'Test', null, null, []);

        $this->assertNull($result);
    }

    public function test_execute_throws_on_validation_error(): void
    {
        $invoice = new InvoiceEntity;
        $invoice->setId(5)->setAccountId(10);

        $this->invoiceService->expects($this->once())
            ->method('getById')
            ->with(5)
            ->willReturn($invoice)
        ;

        $this->validator->expects($this->once())
            ->method('validate')
            ->willThrowException(new ValidationException(['cost' => ['error']]))
        ;

        $this->expectException(ValidationException::class);

        $this->command->execute(5, null, -1.0, 'Test', null, null, []);
    }
}
