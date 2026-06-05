<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Acquiring;

use Core\App\Billing\Acquiring\HandleSubmitWebhookCommand;
use Core\Contracts\DbServiceInterface;
use Core\Domains\Billing\Acquiring\AcquiringEntity;
use Core\Domains\Billing\Acquiring\Enums\ProviderEnum;
use Core\Domains\Billing\Acquiring\Enums\StatusEnum;
use Core\Domains\Billing\Acquiring\Services\AcquiringService;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Payment\PaymentEntity;
use Core\Domains\Billing\Payment\PaymentFactory;
use Core\Domains\Billing\Payment\PaymentService;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Tests\TestCase;

class HandleSubmitWebhookCommandTest extends TestCase
{
    private DbServiceInterface         $dbService;
    private AcquiringService           $acquiringService;
    private PaymentService             $paymentService;
    private InvoiceService             $invoiceService;
    private HistoryChangesService      $historyChangesService;
    private HandleSubmitWebhookCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dbService             = $this->createMock(DbServiceInterface::class);
        $this->acquiringService      = $this->createMock(AcquiringService::class);
        $this->paymentService        = $this->createMock(PaymentService::class);
        $paymentFactory              = new PaymentFactory;
        $this->invoiceService        = $this->createMock(InvoiceService::class);
        $this->historyChangesService = $this->createMock(HistoryChangesService::class);

        $this->command = new HandleSubmitWebhookCommand(
            $this->dbService,
            $this->acquiringService,
            $this->paymentService,
            $paymentFactory,
            $this->invoiceService,
            $this->historyChangesService,
        );
    }

    public function test_execute_processes_payment(): void
    {
        $acquiring = new AcquiringEntity;
        $acquiring->setId(1)->setInvoiceId(10)->setUserId(42)->setAmount(5000.0);
        $acquiring->setStatus(StatusEnum::PROCESS);
        $acquiring->setProvider(ProviderEnum::VTB);
        $expectedHash = $acquiring->makeHash();

        $invoice = new InvoiceEntity;
        $invoice->setId(10)->setAccountId(100);

        $this->acquiringService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($acquiring)
        ;

        $this->invoiceService->expects($this->once())
            ->method('getById')
            ->with(10)
            ->willReturn($invoice)
        ;

        $this->dbService->expects($this->once())->method('beginTransaction');

        $savedPayment = new PaymentEntity;
        $savedPayment->setId(77);

        $this->paymentService->expects($this->once())
            ->method('save')
            ->willReturn($savedPayment)
        ;

        $this->historyChangesService->expects($this->once())
            ->method('writeToHistory')
        ;

        $this->acquiringService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(AcquiringEntity $e) => $e->getStatus() === StatusEnum::PAID && $e->getPaymentId() === 77))
        ;

        $this->dbService->expects($this->once())->method('commit');

        $result = $this->command->execute(1, $expectedHash);

        $this->assertTrue($result);
    }

    public function test_execute_returns_false_when_not_found(): void
    {
        $this->acquiringService->expects($this->once())
            ->method('getById')
            ->with(999)
            ->willReturn(null)
        ;

        $result = $this->command->execute(999, 'hash');

        $this->assertFalse($result);
    }

    public function test_execute_returns_false_when_hash_mismatch(): void
    {
        $acquiring = new AcquiringEntity;
        $acquiring->setId(1)->setInvoiceId(10)->setUserId(42)->setAmount(5000.0);
        $acquiring->setStatus(StatusEnum::PROCESS);

        $this->acquiringService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($acquiring)
        ;

        $this->dbService->expects($this->never())->method('beginTransaction');

        $result = $this->command->execute(1, 'wrong-hash');

        $this->assertFalse($result);
    }

    public function test_execute_returns_false_when_not_in_process(): void
    {
        $acquiring = new AcquiringEntity;
        $acquiring->setId(1)->setInvoiceId(10)->setUserId(42)->setAmount(5000.0);
        $acquiring->setStatus(StatusEnum::CANCELED);

        $this->acquiringService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($acquiring)
        ;

        $this->dbService->expects($this->never())->method('beginTransaction');

        $result = $this->command->execute(1, $acquiring->makeHash());

        $this->assertFalse($result);
    }
}
