<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Payment;

use Core\App\Billing\Payment\CreatePublicPaymentCommand;
use Core\App\Billing\Payment\Validator\CreatePublicPaymentValidator;
use Core\Contracts\DbServiceInterface;
use Core\Domains\Account\AccountEntity;
use Core\Domains\Account\AccountService;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Payment\PaymentEntity;
use Core\Domains\Billing\Payment\PaymentFactory;
use Core\Domains\Billing\Payment\PaymentFileService;
use Core\Domains\Billing\Payment\PaymentService;
use Core\Domains\Shared\ValueObjects\UploadedFile;
use Core\Exceptions\ValidationException;
use Tests\TestCase;

class CreatePublicPaymentCommandTest extends TestCase
{
    private DbServiceInterface           $dbService;
    private PaymentService               $paymentService;
    private PaymentFactory               $paymentFactory;
    private PaymentFileService           $fileService;
    private AccountService               $accountService;
    private InvoiceService               $invoiceService;
    private CreatePublicPaymentValidator $validator;
    private CreatePublicPaymentCommand   $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dbService      = $this->createMock(DbServiceInterface::class);
        $this->paymentService = $this->createMock(PaymentService::class);
        $this->paymentFactory = new PaymentFactory;
        $this->fileService    = $this->createMock(PaymentFileService::class);
        $this->accountService = $this->createMock(AccountService::class);
        $this->invoiceService = $this->createMock(InvoiceService::class);
        $this->validator      = $this->createMock(CreatePublicPaymentValidator::class);

        $this->command = new CreatePublicPaymentCommand(
            $this->dbService,
            $this->paymentService,
            $this->paymentFactory,
            $this->fileService,
            $this->accountService,
            $this->invoiceService,
            $this->validator,
        );
    }

    public function test_execute_creates_payment_with_invoice(): void
    {
        $this->validator->expects($this->once())->method('validate');

        $this->dbService->expects($this->once())
            ->method('transaction')
            ->willReturnCallback(fn(callable $callback) => $callback())
        ;

        $invoice = new InvoiceEntity;
        $invoice->setId(5)->setAccountId(10);

        $this->invoiceService->expects($this->once())
            ->method('getById')
            ->with(5)
            ->willReturn($invoice)
        ;

        $this->paymentService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(PaymentEntity $p) => $p->getInvoiceId() === 5
                                                           && $p->getAccountId() === 10
                                                           && $p->getCost() === 1000.0
                                                           && $p->getComment() === 'text',
            ))
            ->willReturnCallback(fn(PaymentEntity $p) => $p->setId(1))
        ;

        $this->fileService->expects($this->once())
            ->method('storePaymentFiles')
            ->with([], 1)
        ;

        $this->command->execute(5, null, 1000.0, 'text', 'fullText', []);
    }

    public function test_execute_creates_payment_with_account_number(): void
    {
        $this->validator->expects($this->once())->method('validate');

        $this->dbService->expects($this->once())
            ->method('transaction')
            ->willReturnCallback(fn(callable $callback) => $callback())
        ;

        $this->invoiceService->expects($this->never())->method('getById');

        $account = new AccountEntity;
        $account->setId(20);

        $this->accountService->expects($this->once())
            ->method('findByNumber')
            ->with('A-001')
            ->willReturn($account)
        ;

        $this->paymentService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(PaymentEntity $p) => $p->getAccountId() === 20
                                                           && $p->getCost() === 500.0
                                                           && $p->getComment() === 'fullText',
            ))
            ->willReturnCallback(fn(PaymentEntity $p) => $p->setId(2))
        ;

        $this->fileService->expects($this->once())
            ->method('storePaymentFiles')
            ->with([], 2)
        ;

        $this->command->execute(null, 'A-001', 500.0, 'text', 'fullText', []);
    }

    public function test_execute_creates_payment_without_account(): void
    {
        $this->validator->expects($this->once())->method('validate');

        $this->dbService->expects($this->once())
            ->method('transaction')
            ->willReturnCallback(fn(callable $callback) => $callback())
        ;

        $this->invoiceService->expects($this->never())->method('getById');
        $this->accountService->expects($this->never())->method('findByNumber');

        $this->paymentService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(PaymentEntity $p) => $p->getAccountId() === null
                                                           && $p->getCost() === 300.0
                                                           && $p->getComment() === 'fullText',
            ))
            ->willReturnCallback(fn(PaymentEntity $p) => $p->setId(3))
        ;

        $files = [new UploadedFile('pay.jpg', '/tmp/pay.jpg', 'image/jpeg', 1024, 'content')];

        $this->fileService->expects($this->once())
            ->method('storePaymentFiles')
            ->with($files, 3)
        ;

        $this->command->execute(null, null, 300.0, 'text', 'fullText', $files);
    }

    public function test_execute_throws_on_validation_error(): void
    {
        $this->validator->expects($this->once())
            ->method('validate')
            ->willThrowException(new ValidationException(['text' => ['error']]))
        ;

        $this->expectException(ValidationException::class);

        $this->command->execute(null, null, 0.0, '', 'fullText', []);
    }
}
