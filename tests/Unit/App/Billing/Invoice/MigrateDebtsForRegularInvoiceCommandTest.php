<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Invoice;

use Core\App\Billing\Invoice\MigrateDebtsForRegularInvoiceCommand;
use Core\Domains\Account\AccountIdEnum;
use Core\Domains\Billing\Claim\ClaimCollection;
use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\Billing\Claim\ClaimFactory;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\Debt\DebtMigrationService;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Invoice\InvoiceTypeEnum;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceCollection;
use Core\Domains\Billing\Service\ServiceEntity;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use RuntimeException;
use Tests\TestCase;

class MigrateDebtsForRegularInvoiceCommandTest extends TestCase
{
    private InvoiceService                $invoiceService;
    private ClaimService                  $claimService;
    private ClaimFactory                  $claimFactory;
    private ServiceCatalogService         $serviceService;
    private DebtMigrationService          $debtMigrationService;
    private MigrateDebtsForRegularInvoiceCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invoiceService       = $this->createMock(InvoiceService::class);
        $this->claimService         = $this->createMock(ClaimService::class);
        $this->claimFactory         = new ClaimFactory;
        $this->serviceService       = $this->createMock(ServiceCatalogService::class);
        $this->debtMigrationService = $this->createMock(DebtMigrationService::class);

        $this->command = new MigrateDebtsForRegularInvoiceCommand(
            $this->invoiceService,
            $this->claimService,
            $this->claimFactory,
            $this->serviceService,
            $this->debtMigrationService,
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
        $invoice = (new InvoiceEntity)->setId(10)->setType(InvoiceTypeEnum::INCOME);

        $this->invoiceService->method('getById')->with(10)->willReturn($invoice);

        $this->debtMigrationService->expects($this->never())->method('getUnmigratedOldDebts');

        $this->command->execute(10);
    }

    public function test_execute_returns_early_when_snt_account(): void
    {
        $invoice = (new InvoiceEntity)
            ->setId(10)
            ->setType(InvoiceTypeEnum::REGULAR)
            ->setAccountId(AccountIdEnum::SNT->value)
        ;

        $this->invoiceService->method('getById')->with(10)->willReturn($invoice);

        $this->debtMigrationService->expects($this->never())->method('getUnmigratedOldDebts');

        $this->command->execute(10);
    }

    public function test_execute_returns_early_when_no_debt_service(): void
    {
        $invoice = (new InvoiceEntity)
            ->setId(10)
            ->setType(InvoiceTypeEnum::REGULAR)
            ->setPeriodId(3)
            ->setAccountId(5)
        ;

        $oldDebtClaim = (new ClaimEntity)->setId(99)->setCost(300.0)->setPaid(0.0)->setTariff(100.0);

        $this->invoiceService->method('getById')->with(10)->willReturn($invoice);
        $this->debtMigrationService->method('getUnmigratedOldDebts')
            ->with($invoice)
            ->willReturn(new ClaimCollection([$oldDebtClaim]))
        ;
        $this->serviceService->method('getByPeriodId')->with(3)->willReturn(new ServiceCollection);

        $this->claimService->expects($this->never())->method('save');

        $this->command->execute(10);
    }

    public function test_execute_returns_early_when_no_old_debts(): void
    {
        $invoice = (new InvoiceEntity)
            ->setId(10)
            ->setType(InvoiceTypeEnum::REGULAR)
            ->setPeriodId(3)
            ->setAccountId(5)
        ;

        $debtService = (new ServiceEntity)->setId(11)->setType(ServiceTypeEnum::DEBT)->setName('Долг');

        $this->invoiceService->method('getById')->with(10)->willReturn($invoice);
        $this->debtMigrationService->method('getUnmigratedOldDebts')
            ->with($invoice)
            ->willReturn(new ClaimCollection)
        ;
        $this->serviceService->method('getByPeriodId')->with(3)->willReturn(new ServiceCollection([$debtService]));

        $this->claimService->expects($this->never())->method('save');

        $this->command->execute(10);
    }

    public function test_execute_saves_claim_with_resolved_name(): void
    {
        $invoice = (new InvoiceEntity)
            ->setId(10)
            ->setType(InvoiceTypeEnum::REGULAR)
            ->setPeriodId(3)
            ->setAccountId(5)
        ;

        $oldDebtClaim = (new ClaimEntity)
            ->setId(99)
            ->setServiceId(1)
            ->setCost(300.0)
            ->setPaid(0.0)
            ->setTariff(100.0)
        ;

        $debtService = (new ServiceEntity)->setId(11)->setType(ServiceTypeEnum::DEBT)->setName('Долг');

        $this->invoiceService->method('getById')->with(10)->willReturn($invoice);
        $this->debtMigrationService->method('getUnmigratedOldDebts')
            ->with($invoice)
            ->willReturn(new ClaimCollection([$oldDebtClaim]))
        ;
        $this->serviceService->method('getByPeriodId')->with(3)->willReturn(new ServiceCollection([$debtService]));

        $this->debtMigrationService->expects($this->once())
            ->method('resolveDebtClaimName')
            ->with($oldDebtClaim)
            ->willReturn('Членский взнос (долг за период 2024)')
        ;

        $this->claimService->expects($this->once())
            ->method('save')
            ->with($this->callback(function (ClaimEntity $claim) {
                $this->assertSame('Членский взнос (долг за период 2024)', $claim->getName());
                $this->assertSame(11, $claim->getServiceId());
                $this->assertSame(99, $claim->getOriginalClaimId());
                $this->assertSame(1, $claim->getOriginalServiceId());
                $this->assertSame(10, $claim->getInvoiceId());
                $this->assertSame(3.0, $claim->getQuantity());
                $this->assertSame(300.0, $claim->getCost());

                return true;
            }))
            ->willReturnArgument(0)
        ;

        $this->command->execute(10);
    }
}
