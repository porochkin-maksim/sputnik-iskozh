<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Claim;

use Core\App\Billing\Claim\GetListCommand;
use Core\Domains\Billing\Claim\ClaimCollection;
use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\Billing\Claim\ClaimSearcher;
use Core\Domains\Billing\Claim\ClaimSearchResponse;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Period\PeriodEntity;
use Core\Domains\Billing\Period\PeriodService;
use Tests\TestCase;

class GetListCommandTest extends TestCase
{
    private ClaimService   $claimService;
    private InvoiceService $invoiceService;
    private PeriodService  $periodService;
    private GetListCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->claimService   = $this->createMock(ClaimService::class);
        $this->invoiceService = $this->createMock(InvoiceService::class);
        $this->periodService  = $this->createMock(PeriodService::class);

        $this->command = new GetListCommand(
            $this->claimService,
            $this->invoiceService,
            $this->periodService,
        );
    }

    public function test_execute_returns_sorted_claims(): void
    {
        $invoice = new InvoiceEntity;
        $invoice->setId(1)->setPeriodId(5);

        $this->invoiceService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($invoice)
        ;

        $this->periodService->expects($this->once())
            ->method('getById')
            ->with(5)
            ->willReturn(new PeriodEntity)
        ;

        $claim1 = new ClaimEntity;
        $claim1->setId(1)->setInvoiceId(1);

        $claimResponse = new ClaimSearchResponse;
        $claimResponse->setItems(new ClaimCollection([$claim1]));

        $this->claimService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(ClaimSearcher::class))
            ->willReturn($claimResponse)
        ;

        $result = $this->command->execute(1);

        $this->assertNotNull($result);
        $this->assertCount(1, $result);
    }

    public function test_execute_returns_null_when_invoice_not_found(): void
    {
        $this->invoiceService->expects($this->once())
            ->method('getById')
            ->with(999)
            ->willReturn(null)
        ;

        $this->claimService->expects($this->never())->method('search');

        $result = $this->command->execute(999);

        $this->assertNull($result);
    }
}
