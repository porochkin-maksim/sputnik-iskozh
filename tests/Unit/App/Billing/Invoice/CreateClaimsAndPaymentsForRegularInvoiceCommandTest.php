<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Invoice;

use Core\App\Billing\Invoice\CreateClaimsAndPaymentsForRegularInvoiceCommand;
use Core\App\Billing\Invoice\CreateClaimsForRegularInvoiceCommand;
use Core\App\Billing\Invoice\MigrateDebtsForRegularInvoiceCommand;
use Core\App\Billing\Invoice\RecalcClaimsPaidCommand;
use Core\Domains\Account\AccountIdEnum;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Invoice\InvoiceTypeEnum;
use Core\Domains\Billing\Period\PeriodCollection;
use Core\Domains\Billing\Period\PeriodService;
use RuntimeException;
use Tests\TestCase;

class CreateClaimsAndPaymentsForRegularInvoiceCommandTest extends TestCase
{
    private InvoiceService                                  $invoiceService;
    private CreateClaimsForRegularInvoiceCommand            $createClaimsCommand;
    private MigrateDebtsForRegularInvoiceCommand            $migrateDebtsCommand;
    private RecalcClaimsPaidCommand                         $claimsPaidCommand;
    private PeriodService                                   $periodService;
    private CreateClaimsAndPaymentsForRegularInvoiceCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invoiceService      = $this->createMock(InvoiceService::class);
        $this->createClaimsCommand = $this->createMock(CreateClaimsForRegularInvoiceCommand::class);
        $this->migrateDebtsCommand = $this->createMock(MigrateDebtsForRegularInvoiceCommand::class);
        $this->claimsPaidCommand   = $this->createMock(RecalcClaimsPaidCommand::class);
        $this->periodService       = $this->createMock(PeriodService::class);

        $this->command = new CreateClaimsAndPaymentsForRegularInvoiceCommand(
            $this->invoiceService,
            $this->createClaimsCommand,
            $this->migrateDebtsCommand,
            $this->claimsPaidCommand,
            $this->periodService,
        );
    }

    public function test_execute_throws_when_invoice_not_found(): void
    {
        $this->invoiceService->method('getById')->with(999)->willReturn(null);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Счёт не найден #999');

        $this->command->execute(999);
    }

    public function test_execute_returns_early_when_not_regular_type(): void
    {
        $invoice = new InvoiceEntity;
        $invoice->setType(InvoiceTypeEnum::INCOME);

        $this->invoiceService->method('getById')->with(1)->willReturn($invoice);

        $this->createClaimsCommand->expects($this->never())->method('execute');
        $this->migrateDebtsCommand->expects($this->never())->method('execute');

        $this->command->execute(1);
    }

    public function test_execute_returns_early_when_snt_account(): void
    {
        $invoice = new InvoiceEntity;
        $invoice->setType(InvoiceTypeEnum::REGULAR);
        $invoice->setAccountId(AccountIdEnum::SNT->value);

        $this->invoiceService->method('getById')->with(1)->willReturn($invoice);

        $this->createClaimsCommand->expects($this->never())->method('execute');
        $this->migrateDebtsCommand->expects($this->never())->method('execute');

        $this->command->execute(1);
    }

    public function test_execute_migrates_debts_when_single_active_period(): void
    {
        $invoice = new InvoiceEntity;
        $invoice->setId(10)->setType(InvoiceTypeEnum::REGULAR)->setPeriodId(5)->setAccountId(100);

        $this->invoiceService->method('getById')->with(10)->willReturn($invoice);
        $this->periodService->method('getOpenPeriods')->willReturn(new PeriodCollection([$invoice]));

        $this->migrateDebtsCommand->expects($this->once())->method('execute')->with(10);
        $this->createClaimsCommand->expects($this->once())->method('execute')->with(10);
        $this->claimsPaidCommand->expects($this->once())->method('execute')->with(10);

        $this->command->execute(10);
    }

    public function test_execute_skips_debt_migration_when_multiple_active_periods(): void
    {
        $invoice1 = new InvoiceEntity;
        $invoice1->setId(10)->setType(InvoiceTypeEnum::REGULAR)->setPeriodId(5)->setAccountId(100);

        $invoice2 = new InvoiceEntity;
        $invoice2->setId(20)->setType(InvoiceTypeEnum::REGULAR)->setPeriodId(6)->setAccountId(200);

        $this->invoiceService->method('getById')->with(10)->willReturn($invoice1);
        $this->periodService->method('getOpenPeriods')->willReturn(new PeriodCollection([$invoice1, $invoice2]));

        $this->migrateDebtsCommand->expects($this->never())->method('execute');
        $this->createClaimsCommand->expects($this->once())->method('execute')->with(10);
        $this->claimsPaidCommand->expects($this->once())->method('execute')->with(10);

        $this->command->execute(10);
    }
}
