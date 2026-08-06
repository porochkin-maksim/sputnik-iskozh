<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Claim;

use Core\App\Billing\Claim\CheckDebtClaimNamesCommand;
use Core\Domains\Billing\Claim\ClaimCollection;
use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\Billing\Claim\ClaimSearchResponse;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\Debt\DebtMigrationService;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceCollection;
use Core\Domains\Billing\Service\ServiceEntity;
use Core\Domains\Billing\Service\ServiceSearchResponse;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use Tests\TestCase;

class CheckDebtClaimNamesCommandTest extends TestCase
{
    private ClaimService                $claimService;
    private ServiceCatalogService       $serviceService;
    private DebtMigrationService        $debtMigrationService;
    private CheckDebtClaimNamesCommand  $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->claimService        = $this->createMock(ClaimService::class);
        $this->serviceService      = $this->createMock(ServiceCatalogService::class);
        $this->debtMigrationService = $this->createMock(DebtMigrationService::class);

        $this->command = new CheckDebtClaimNamesCommand(
            $this->claimService,
            $this->serviceService,
            $this->debtMigrationService,
        );
    }

    public function test_execute_returns_empty_when_no_debt_services(): void
    {
        $serviceResponse = new ServiceSearchResponse;
        $serviceResponse->setItems(new ServiceCollection);

        $this->serviceService->method('search')
            ->with($this->isInstanceOf(\Core\Domains\Billing\Service\ServiceSearcher::class))
            ->willReturn($serviceResponse)
        ;

        $this->claimService->expects($this->never())->method('search');

        $result = $this->command->execute(true);

        $this->assertSame(0, $result['checked']);
        $this->assertSame(0, $result['broken']);
        $this->assertSame(0, $result['fixed']);
        $this->assertSame([], $result['items']);
    }

    public function test_execute_reports_broken_names_without_fix(): void
    {
        $brokenClaim = (new ClaimEntity)->setId(5291)->setName('Долг (долг за период 2025)');
        $validClaim  = (new ClaimEntity)->setId(2092)->setName('Членский взнос (долг за период 2024)');

        $this->stubDebtServices();
        $this->stubClaims([$brokenClaim, $validClaim]);

        $this->debtMigrationService->method('resolveDebtClaimName')
            ->willReturnCallback(static fn(ClaimEntity $claim) => match ($claim->getId()) {
                5291 => 'Членский взнос (долг за период 2024)',
                default => $claim->getName(),
            })
        ;

        $this->claimService->expects($this->never())->method('save');

        $result = $this->command->execute(false);

        $this->assertSame(2, $result['checked']);
        $this->assertSame(1, $result['broken']);
        $this->assertSame(0, $result['fixed']);
        $this->assertSame(5291, $result['items'][0]['id']);
        $this->assertSame('Долг (долг за период 2025)', $result['items'][0]['old']);
        $this->assertSame('Членский взнос (долг за период 2024)', $result['items'][0]['new']);
    }

    public function test_execute_fixes_names_with_fix(): void
    {
        $brokenClaim = (new ClaimEntity)->setId(5291)->setName('Долг (долг за период 2025)');

        $this->stubDebtServices();
        $this->stubClaims([$brokenClaim]);

        $this->debtMigrationService->method('resolveDebtClaimName')
            ->willReturn('Членский взнос (долг за период 2024)')
        ;

        $this->claimService->expects($this->once())
            ->method('save')
            ->with($this->callback(function (ClaimEntity $claim) {
                $this->assertSame('Членский взнос (долг за период 2024)', $claim->getName());

                return true;
            }))
            ->willReturnArgument(0)
        ;

        $result = $this->command->execute(true);

        $this->assertSame(1, $result['checked']);
        $this->assertSame(1, $result['broken']);
        $this->assertSame(1, $result['fixed']);
    }

    private function stubDebtServices(): void
    {
        $debtService = (new ServiceEntity)->setId(11)->setType(ServiceTypeEnum::DEBT)->setName('Долг');

        $serviceResponse = new ServiceSearchResponse;
        $serviceResponse->setItems(new ServiceCollection([$debtService]));

        $this->serviceService->method('search')
            ->willReturn($serviceResponse)
        ;
    }

    /**
     * @param ClaimEntity[] $claims
     */
    private function stubClaims(array $claims): void
    {
        $claimResponse = new ClaimSearchResponse;
        $claimResponse->setItems(new ClaimCollection($claims));

        $this->claimService->method('search')
            ->willReturn($claimResponse)
        ;
    }
}
