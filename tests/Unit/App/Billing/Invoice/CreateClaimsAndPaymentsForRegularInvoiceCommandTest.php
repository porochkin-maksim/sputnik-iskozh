<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Invoice;

use Core\App\Billing\Invoice\CreateClaimsAndPaymentsForRegularInvoiceCommand;
use Core\Domains\Account\AccountEntity;
use Core\Domains\Account\AccountIdEnum;
use Core\Domains\Account\AccountService;
use Core\Domains\Billing\Claim\ClaimCollection;
use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\Billing\Claim\ClaimFactory;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\Debt\DebtMigrationService;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Invoice\InvoiceTypeEnum;
use Core\App\Billing\Invoice\RecalcClaimsPaidCommand;
use Core\Domains\Billing\Period\PeriodEntity;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceCollection;
use Core\Domains\Billing\Service\ServiceEntity;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use RuntimeException;
use Tests\TestCase;

class CreateClaimsAndPaymentsForRegularInvoiceCommandTest extends TestCase
{
    private InvoiceService                                  $invoiceService;
    private ClaimService                                    $claimService;
    private ClaimFactory                                    $claimFactory;
    private AccountService                                  $accountService;
    private ServiceCatalogService                           $serviceService;
    private DebtMigrationService                            $debtMigrationService;
    private RecalcClaimsPaidCommand                         $claimsPaidCommand;
    private CreateClaimsAndPaymentsForRegularInvoiceCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invoiceService       = $this->createMock(InvoiceService::class);
        $this->claimService         = $this->createMock(ClaimService::class);
        $this->claimFactory         = new ClaimFactory;
        $this->accountService       = $this->createMock(AccountService::class);
        $this->serviceService       = $this->createMock(ServiceCatalogService::class);
        $this->debtMigrationService = $this->createMock(DebtMigrationService::class);
        $this->claimsPaidCommand    = $this->createMock(RecalcClaimsPaidCommand::class);

        $this->command = new CreateClaimsAndPaymentsForRegularInvoiceCommand(
            $this->invoiceService,
            $this->claimService,
            $this->claimFactory,
            $this->accountService,
            $this->serviceService,
            $this->debtMigrationService,
            $this->claimsPaidCommand,
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

        $this->claimService->expects($this->never())->method('save');

        $this->command->execute(1);
    }

    public function test_execute_returns_early_when_snt_account(): void
    {
        $invoice = new InvoiceEntity;
        $invoice->setType(InvoiceTypeEnum::REGULAR);
        $invoice->setAccountId(AccountIdEnum::SNT->value);

        $this->invoiceService->method('getById')->with(1)->willReturn($invoice);

        $this->claimService->expects($this->never())->method('save');

        $this->command->execute(1);
    }

    public function test_execute_creates_claims_and_payment(): void
    {
        $invoice = new InvoiceEntity;
        $invoice->setId(10)->setType(InvoiceTypeEnum::REGULAR)->setPeriodId(5)->setAccountId(100);

        $this->invoiceService->method('getById')->with(10)->willReturn($invoice);

        $previousPeriod = new PeriodEntity;
        $previousPeriod->setId(4)->setName('Май 2026');

        $previousInvoice = new InvoiceEntity;
        $previousInvoice->setId(9)->setPeriodId(4)->setAccountId(100);
        $previousInvoice->setPeriod($previousPeriod);

        $oldClaim = new ClaimEntity;
        $oldClaim->setId(1)->setCost(100.0)->setPaid(50.0)->setTariff(100.0);
        $oldClaimService = new ServiceEntity;
        $oldClaimService->setName('Членский взнос')->setType(ServiceTypeEnum::MEMBERSHIP_FEE);
        $oldClaim->setService($oldClaimService);
        $oldClaim->setInvoice($previousInvoice);

        $this->debtMigrationService->method('getUnmigratedOldDebts')
            ->with($invoice)
            ->willReturn(new ClaimCollection([$oldClaim]))
        ;

        $newDebtService = new ServiceEntity;
        $newDebtService->setId(20)->setType(ServiceTypeEnum::DEBT);

        $membershipService = new ServiceEntity;
        $membershipService->setId(21)->setType(ServiceTypeEnum::MEMBERSHIP_FEE)->setCost(500.0);

        $this->serviceService->method('getByPeriodId')
            ->with(5)
            ->willReturn(new ServiceCollection([$newDebtService, $membershipService]))
        ;

        $account = new AccountEntity;
        $account->setId(100)->setSize(6);

        $this->accountService->method('getById')->with(100)->willReturn($account);

        $this->claimService->expects($this->exactly(2))
            ->method('save')
            ->willReturnCallback(fn(ClaimEntity $c) => $c->setId(random_int(100, 999)))
        ;

        $this->command->execute(10);
    }
}
