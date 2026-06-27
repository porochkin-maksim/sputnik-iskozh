<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Payment;

use Core\App\Billing\Invoice\RecalcClaimsPaidCommand;
use Core\App\Billing\Payment\SaveImportPaymentsCommand;
use Core\App\Billing\Payment\SaveImportPaymentsInput;
use Core\Domains\Billing\Events\ImportPaymentData;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Payment\PaymentEntity;
use Core\Domains\Billing\Payment\PaymentFactory;
use Core\Domains\Billing\Payment\PaymentTransactionService;
use Tests\TestCase;

class SaveImportPaymentsCommandTest extends TestCase
{
    private PaymentFactory            $paymentFactory;
    private PaymentTransactionService $paymentTransactionService;
    private InvoiceService            $invoiceService;
    private RecalcClaimsPaidCommand   $recalcClaimsPaidCommand;
    private SaveImportPaymentsCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentFactory            = new PaymentFactory;
        $this->paymentTransactionService = $this->createMock(PaymentTransactionService::class);
        $this->invoiceService            = $this->createMock(InvoiceService::class);
        $this->recalcClaimsPaidCommand   = $this->createMock(RecalcClaimsPaidCommand::class);

        $this->command = new SaveImportPaymentsCommand(
            $this->paymentFactory,
            $this->paymentTransactionService,
            $this->invoiceService,
            $this->recalcClaimsPaidCommand,
        );
    }

    public function test_execute_saves_valid_payments(): void
    {
        $this->paymentTransactionService->expects($this->exactly(2))
            ->method('saveWithTransaction')
            ->with($this->callback(fn(PaymentEntity $p) => $p->isVerified() === true
                                                           && $p->isModerated() === true
                                                           && $p->getName() === 'Импортированный платёж'
                                                           && in_array($p->getCost(), [1000.0, 2000.0], true),
            ))
        ;

        $input = new SaveImportPaymentsInput([
            new ImportPaymentData(1, 1000.0),
            new ImportPaymentData(2, 2000.0),
        ]);

        $this->command->execute($input);
    }

    public function test_execute_skips_invalid_data(): void
    {
        $this->paymentTransactionService->expects($this->exactly(2))
            ->method('saveWithTransaction')
        ;

        $input = new SaveImportPaymentsInput([
            new ImportPaymentData(0, 1000.0),
            new ImportPaymentData(1, 0.0),
            new ImportPaymentData(-1, 500.0),
            new ImportPaymentData(3, -10.0),
            new ImportPaymentData(5, 3000.0),
            new ImportPaymentData(6, 500.0),
        ]);

        $this->command->execute($input);
    }

    public function test_execute_skips_all_invalid_data(): void
    {
        $this->paymentTransactionService->expects($this->never())->method('saveWithTransaction');

        $input = new SaveImportPaymentsInput([
            new ImportPaymentData(0, 1000.0),
            new ImportPaymentData(1, 0.0),
        ]);

        $this->command->execute($input);
    }

    public function test_execute_with_empty_input(): void
    {
        $this->paymentTransactionService->expects($this->never())->method('saveWithTransaction');

        $input = new SaveImportPaymentsInput([]);

        $this->command->execute($input);
    }
}
