<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Invoice;

use Core\App\Billing\Invoice\CreateClaimsAndPaymentsForRegularInvoiceCommand;
use Core\Domains\Account\AccountEntity;
use Core\Domains\Account\AccountIdEnum;
use Core\Domains\Account\AccountService;
use Core\Domains\Billing\Claim\ClaimCollection;
use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\Billing\Claim\ClaimFactory;
use Core\Domains\Billing\Claim\ClaimSearcher;
use Core\Domains\Billing\Claim\ClaimSearchResponse;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\Invoice\InvoiceCollection;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceSearcher;
use Core\Domains\Billing\Invoice\InvoiceSearchResponse;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Invoice\InvoiceTypeEnum;
use Core\Domains\Billing\Payment\PaymentFactory;
use Core\Domains\Billing\Payment\PaymentService;
use Core\Domains\Billing\Period\PeriodCollection;
use Core\Domains\Billing\Period\PeriodEntity;
use Core\Domains\Billing\Period\PeriodSearcher;
use Core\Domains\Billing\Period\PeriodSearchResponse;
use Core\Domains\Billing\Period\PeriodService;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceCollection;
use Core\Domains\Billing\Service\ServiceEntity;
use Core\Domains\Billing\Service\ServiceSearcher;
use Core\Domains\Billing\Service\ServiceSearchResponse;
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
    private PaymentFactory                                  $paymentFactory;
    private PaymentService                                  $paymentService;
    private PeriodService                                   $periodService;
    private CreateClaimsAndPaymentsForRegularInvoiceCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invoiceService = $this->createMock(InvoiceService::class);
        $this->claimService   = $this->createMock(ClaimService::class);
        $this->claimFactory   = new ClaimFactory;
        $this->accountService = $this->createMock(AccountService::class);
        $this->serviceService = $this->createMock(ServiceCatalogService::class);
        $this->paymentFactory = new PaymentFactory;
        $this->paymentService = $this->createMock(PaymentService::class);
        $this->periodService  = $this->createMock(PeriodService::class);

        $this->command = new CreateClaimsAndPaymentsForRegularInvoiceCommand(
            $this->invoiceService,
            $this->claimService,
            $this->claimFactory,
            $this->accountService,
            $this->serviceService,
            $this->paymentFactory,
            $this->paymentService,
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

        $periodResponse = new PeriodSearchResponse;
        $periodResponse->setItems(new PeriodCollection([$previousPeriod]));
        $this->periodService->method('search')
            ->with($this->isInstanceOf(PeriodSearcher::class))
            ->willReturn($periodResponse)
        ;

        $previousInvoice = new InvoiceEntity;
        $previousInvoice->setId(9)->setPeriodId(4)->setAccountId(100);
        $previousInvoice->setPeriod($previousPeriod);

        $prevInvoiceResponse = new InvoiceSearchResponse;
        $prevInvoiceResponse->setItems(new InvoiceCollection([$previousInvoice]));

        $this->invoiceService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(InvoiceSearcher::class))
            ->willReturn($prevInvoiceResponse)
        ;

        $oldClaim = new ClaimEntity;
        $oldClaim->setId(1)->setCost(100.0)->setPaid(50.0)->setTariff(100.0);
        $oldClaimService = new ServiceEntity;
        $oldClaimService->setName('Членский взнос')->setType(ServiceTypeEnum::MEMBERSHIP_FEE);
        $oldClaim->setService($oldClaimService);

        $claimResponse = new ClaimSearchResponse;
        $claimResponse->setItems(new ClaimCollection([$oldClaim]));

        $this->claimService->method('search')
            ->with($this->isInstanceOf(ClaimSearcher::class))
            ->willReturn($claimResponse)
        ;

        $newDebtService = new ServiceEntity;
        $newDebtService->setId(20)->setType(ServiceTypeEnum::DEBT);

        $membershipService = new ServiceEntity;
        $membershipService->setId(21)->setType(ServiceTypeEnum::MEMBERSHIP_FEE)->setCost(500.0);

        $serviceResponse = new ServiceSearchResponse;
        $serviceResponse->setItems(new ServiceCollection([$newDebtService, $membershipService]));

        $this->serviceService->method('search')
            ->with($this->isInstanceOf(ServiceSearcher::class))
            ->willReturn($serviceResponse)
        ;

        $account = new AccountEntity;
        $account->setId(100)->setSize(6);

        $this->accountService->method('getById')->with(100)->willReturn($account);

        $this->claimService->expects($this->exactly(2))
            ->method('save')
            ->willReturnCallback(fn(ClaimEntity $c) => $c->setId(random_int(100, 999)))
        ;

        $this->paymentService->expects($this->never())->method('save');

        $this->command->execute(10);
    }
}
