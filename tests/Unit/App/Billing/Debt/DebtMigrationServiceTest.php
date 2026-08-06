<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Debt;

use Core\Domains\Billing\Claim\ClaimCollection;
use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\Billing\Claim\ClaimSearcher;
use Core\Domains\Billing\Claim\ClaimSearchResponse;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\Debt\DebtMigrationService;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Period\PeriodEntity;
use Core\Domains\Billing\Period\PeriodService;
use Core\Domains\Billing\Service\ServiceEntity;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use Tests\TestCase;

class DebtMigrationServiceTest extends TestCase
{
    private PeriodService           $periodService;
    private InvoiceService          $invoiceService;
    private ClaimService            $claimService;
    private DebtMigrationService    $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->periodService  = $this->createMock(PeriodService::class);
        $this->invoiceService = $this->createMock(InvoiceService::class);
        $this->claimService   = $this->createMock(ClaimService::class);

        $this->service = new DebtMigrationService(
            $this->periodService,
            $this->invoiceService,
            $this->claimService,
        );
    }

    public function test_resolve_keeps_existing_valid_name(): void
    {
        $claim = (new ClaimEntity)->setName('Членский взнос (долг за период 2024)');

        $this->assertSame('Членский взнос (долг за период 2024)', $this->service->resolveDebtClaimName($claim));
    }

    public function test_resolve_fixes_broken_name_from_original_claim(): void
    {
        $claim = (new ClaimEntity)
            ->setId(5291)
            ->setName('Долг (долг за период 2025)')
            ->setOriginalClaimId(2104)
            ->setOriginalService((new ServiceEntity)->setName('Долг')->setType(ServiceTypeEnum::DEBT))
        ;

        $original = (new ClaimEntity)->setId(2104)->setName('Членский взнос (долг за период 2024)');

        $response = new ClaimSearchResponse;
        $response->setItems(new ClaimCollection([$original]));

        $this->claimService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(ClaimSearcher::class))
            ->willReturn($response)
        ;

        $this->assertSame('Членский взнос (долг за период 2024)', $this->service->resolveDebtClaimName($claim));
    }

    public function test_resolve_walks_chain_until_valid_name(): void
    {
        $claim = (new ClaimEntity)
            ->setId(9000)
            ->setName('Долг (долг за период 2025)')
            ->setOriginalClaimId(2104)
        ;

        $middle = (new ClaimEntity)
            ->setId(2104)
            ->setName('Долг (долг за период 2024)')
            ->setOriginalClaimId(39)
        ;

        $source = (new ClaimEntity)->setId(39)->setName('Членский взнос (долг за период 2024)');

        $response1 = new ClaimSearchResponse;
        $response1->setItems(new ClaimCollection([$middle]));
        $response2 = new ClaimSearchResponse;
        $response2->setItems(new ClaimCollection([$source]));

        $this->claimService->expects($this->exactly(2))
            ->method('search')
            ->with($this->isInstanceOf(ClaimSearcher::class))
            ->willReturnOnConsecutiveCalls($response1, $response2)
        ;

        $this->assertSame('Членский взнос (долг за период 2024)', $this->service->resolveDebtClaimName($claim));
    }

    public function test_resolve_builds_name_from_service_and_period(): void
    {
        $claim = (new ClaimEntity)
            ->setName(null)
            ->setService((new ServiceEntity)->setName('Членский взнос'))
            ->setInvoice((new InvoiceEntity)->setPeriod((new PeriodEntity)->setName('2024')))
        ;

        $this->assertSame('Членский взнос (долг за период 2024)', $this->service->resolveDebtClaimName($claim));
    }

    public function test_resolve_uses_explicit_period_name_override(): void
    {
        $claim = (new ClaimEntity)
            ->setName(null)
            ->setService((new ServiceEntity)->setName('Членский взнос'))
        ;

        $this->assertSame('Членский взнос (долг за период 2025)', $this->service->resolveDebtClaimName($claim, '2025'));
    }

    public function test_resolve_returns_base_name_when_no_period_available(): void
    {
        $claim = (new ClaimEntity)
            ->setName(null)
            ->setService((new ServiceEntity)->setName('Членский взнос'))
        ;

        $this->assertSame('Членский взнос', $this->service->resolveDebtClaimName($claim));
    }
}
