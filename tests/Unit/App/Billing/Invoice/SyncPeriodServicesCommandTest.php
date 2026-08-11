<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Invoice;

use Core\App\Billing\Invoice\RecalcClaimsPaidCommand;
use Core\App\Billing\Invoice\SyncPeriodServicesCommand;
use Core\Domains\Account\AccountService;
use Core\Domains\Billing\Claim\ClaimCollection;
use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\Billing\Claim\ClaimFactory;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\Invoice\InvoiceCollection;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceSearchResponse;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Invoice\InvoiceTypeEnum;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceCollection;
use Core\Domains\Billing\Service\ServiceEntity;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use Tests\TestCase;

class SyncPeriodServicesCommandTest extends TestCase
{
    private InvoiceService            $invoiceService;
    private ClaimService              $claimService;
    private ClaimFactory              $claimFactory;
    private AccountService            $accountService;
    private ServiceCatalogService     $serviceService;
    private RecalcClaimsPaidCommand   $claimsPaidCommand;
    private SyncPeriodServicesCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invoiceService    = $this->createMock(InvoiceService::class);
        $this->claimService      = $this->createMock(ClaimService::class);
        $this->claimFactory      = new ClaimFactory;
        $this->accountService    = $this->createMock(AccountService::class);
        $this->serviceService    = $this->createMock(ServiceCatalogService::class);
        $this->claimsPaidCommand = $this->createMock(RecalcClaimsPaidCommand::class);

        $this->command = new SyncPeriodServicesCommand(
            $this->invoiceService,
            $this->claimService,
            $this->claimFactory,
            $this->accountService,
            $this->serviceService,
            $this->claimsPaidCommand,
        );
    }

    public function test_execute_returns_empty_when_no_period_services(): void
    {
        $this->serviceService->method('getByPeriodId')->with(5)->willReturn(new ServiceCollection);

        $this->invoiceService->expects($this->never())->method('search');
        $this->claimService->expects($this->never())->method('getByInvoiceIdSorted');

        $result = $this->command->execute(5);

        $this->assertSame(0, $result['added']);
        $this->assertSame(0, $result['removed']);
        $this->assertSame(0, $result['skipped']);
        $this->assertSame(0, $result['processed']);
    }

    public function test_execute_removes_orphaned_claim(): void
    {
        $periodService = (new ServiceEntity)->setId(11)->setType(ServiceTypeEnum::TARGET_FEE)->setCost(100.0);
        $invoice       = (new InvoiceEntity)->setId(10)->setType(InvoiceTypeEnum::REGULAR)->setAccountId(5);

        $orphanedClaim = (new ClaimEntity)
            ->setId(1)
            ->setInvoiceId(10)
            ->setServiceId(999)
            ->setPaid(0.0)
        ;

        $this->stubPeriodServices([$periodService]);
        $this->stubInvoices([$invoice]);
        $this->stubClaims([$orphanedClaim]);

        $this->claimService->expects($this->once())
            ->method('deleteById')
            ->with(1)
            ->willReturn(true)
        ;

        $result = $this->command->execute(5);

        $this->assertSame(1, $result['removed']);
        $this->assertSame(1, $result['processed']);
    }

    public function test_execute_keeps_migrated_debt_claim(): void
    {
        $debtService = (new ServiceEntity)->setId(11)->setType(ServiceTypeEnum::DEBT)->setName('Долг');
        $invoice     = (new InvoiceEntity)->setId(10)->setType(InvoiceTypeEnum::REGULAR)->setAccountId(5);

        $debtClaim = (new ClaimEntity)
            ->setId(1)
            ->setInvoiceId(10)
            ->setServiceId(999)
            ->setOriginalClaimId(9999)
            ->setPaid(0.0)
        ;

        $this->stubPeriodServices([$debtService]);
        $this->stubInvoices([$invoice]);
        $this->stubClaims([$debtClaim]);

        $this->claimService->expects($this->never())->method('deleteById');

        $result = $this->command->execute(5);

        $this->assertSame(0, $result['removed']);
        $this->assertSame(0, $result['skipped']);
    }

    public function test_execute_keeps_paid_claim(): void
    {
        $periodService = (new ServiceEntity)->setId(11)->setType(ServiceTypeEnum::TARGET_FEE)->setCost(100.0);
        $invoice       = (new InvoiceEntity)->setId(10)->setType(InvoiceTypeEnum::REGULAR)->setAccountId(5);

        $paidClaim = (new ClaimEntity)
            ->setId(1)
            ->setInvoiceId(10)
            ->setServiceId(999)
            ->setPaid(50.0)
        ;

        $this->stubPeriodServices([$periodService]);
        $this->stubInvoices([$invoice]);
        $this->stubClaims([$paidClaim]);

        $this->claimService->expects($this->never())->method('deleteById');

        $result = $this->command->execute(5);

        $this->assertSame(0, $result['removed']);
        $this->assertSame(1, $result['skipped']);
    }

    public function test_execute_adds_missing_claimable_service(): void
    {
        $periodService = (new ServiceEntity)->setId(11)->setType(ServiceTypeEnum::TARGET_FEE)->setCost(100.0);
        $invoice       = (new InvoiceEntity)->setId(10)->setType(InvoiceTypeEnum::REGULAR)->setAccountId(5);

        $this->stubPeriodServices([$periodService]);
        $this->stubInvoices([$invoice]);
        $this->stubClaims([]);

        $this->claimService->expects($this->once())
            ->method('save')
            ->with($this->callback(function (ClaimEntity $claim) {
                $this->assertSame(11, $claim->getServiceId());
                $this->assertSame(10, $claim->getInvoiceId());
                $this->assertSame(100.0, $claim->getTariff());

                return true;
            }))
            ->willReturnArgument(0)
        ;

        $this->claimsPaidCommand->expects($this->once())
            ->method('execute')
            ->with(10)
        ;

        $result = $this->command->execute(5);

        $this->assertSame(1, $result['added']);
    }

    public function test_execute_keeps_claim_of_current_service(): void
    {
        $periodService = (new ServiceEntity)->setId(11)->setType(ServiceTypeEnum::TARGET_FEE)->setCost(100.0);
        $invoice       = (new InvoiceEntity)->setId(10)->setType(InvoiceTypeEnum::REGULAR)->setAccountId(5);

        $existingClaim = (new ClaimEntity)
            ->setId(1)
            ->setInvoiceId(10)
            ->setServiceId(11)
            ->setPaid(0.0)
        ;

        $this->stubPeriodServices([$periodService]);
        $this->stubInvoices([$invoice]);
        $this->stubClaims([$existingClaim]);

        $this->claimService->expects($this->never())->method('deleteById');
        $this->claimService->expects($this->never())->method('save');

        $result = $this->command->execute(5);

        $this->assertSame(0, $result['removed']);
        $this->assertSame(0, $result['added']);
    }

    /**
     * @param ServiceEntity[] $services
     */
    private function stubPeriodServices(array $services): void
    {
        $this->serviceService->method('getByPeriodId')->with(5)->willReturn(new ServiceCollection($services));
    }

    /**
     * @param InvoiceEntity[] $invoices
     */
    private function stubInvoices(array $invoices): void
    {
        $response = new InvoiceSearchResponse;
        $response->setItems(new InvoiceCollection($invoices));

        $this->invoiceService->method('search')->willReturn($response);
    }

    /**
     * @param ClaimEntity[] $claims
     */
    private function stubClaims(array $claims): void
    {
        $this->claimService->method('getByInvoiceIdSorted')->with(10)->willReturn(new ClaimCollection($claims));
    }
}
