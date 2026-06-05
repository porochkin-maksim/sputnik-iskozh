<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Claim;

use Core\App\Billing\Claim\GetFormDataCommand;
use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\Billing\Claim\ClaimFactory;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Invoice\InvoiceTypeEnum;
use Core\Domains\Billing\Period\PeriodEntity;
use Core\Domains\Billing\Period\PeriodService;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceEntity;
use Tests\TestCase;

class GetFormDataCommandTest extends TestCase
{
    private ClaimFactory          $claimFactory;
    private ClaimService          $claimService;
    private InvoiceService        $invoiceService;
    private ServiceCatalogService $serviceService;
    private PeriodService         $periodService;
    private GetFormDataCommand    $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->claimFactory   = new ClaimFactory;
        $this->claimService   = $this->createMock(ClaimService::class);
        $this->invoiceService = $this->createMock(InvoiceService::class);
        $this->serviceService = $this->createMock(ServiceCatalogService::class);
        $this->periodService  = $this->createMock(PeriodService::class);

        $this->command = new GetFormDataCommand(
            $this->claimFactory,
            $this->claimService,
            $this->invoiceService,
            $this->serviceService,
            $this->periodService,
        );
    }

    public function test_create_returns_form_data(): void
    {
        $invoice = new InvoiceEntity;
        $invoice->setId(1)->setPeriodId(5)->setType(InvoiceTypeEnum::REGULAR)->setServiceId(10);

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

        $service = new ServiceEntity;
        $service->setId(10)->setName('Услуга 1');

        $this->serviceService->expects($this->once())
            ->method('getById')
            ->with(10)
            ->willReturn($service)
        ;

        $result = $this->command->create(1);

        $this->assertNotNull($result);
        $this->assertSame(1, $result->claim->getInvoiceId());
        $this->assertSame(10, $result->claim->getServiceId());
    }

    public function test_create_returns_null_when_invoice_not_found(): void
    {
        $this->invoiceService->expects($this->once())
            ->method('getById')
            ->with(999)
            ->willReturn(null)
        ;

        $result = $this->command->create(999);
        $this->assertNull($result);
    }

    public function test_get_returns_form_data(): void
    {
        $invoice = new InvoiceEntity;
        $invoice->setId(1)->setPeriodId(5)->setType(InvoiceTypeEnum::REGULAR)->setServiceId(10);

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

        $claim = new ClaimEntity;
        $claim->setId(100)->setInvoiceId(1)->setServiceId(10);

        $this->claimService->expects($this->once())
            ->method('getById')
            ->with(100)
            ->willReturn($claim)
        ;

        $claimService = new ServiceEntity;
        $claimService->setId(10)->setName('Электричество');

        $this->serviceService->expects($this->exactly(2))
            ->method('getById')
            ->with(10)
            ->willReturn($claimService)
        ;

        $result = $this->command->get(1, 100);

        $this->assertNotNull($result);
        $this->assertSame(100, $result->claim->getId());
        $this->assertSame(10, $result->claim->getServiceId());
    }

    public function test_get_returns_null_when_invoice_not_found(): void
    {
        $this->invoiceService->expects($this->once())
            ->method('getById')
            ->with(999)
            ->willReturn(null)
        ;

        $result = $this->command->get(999, 100);
        $this->assertNull($result);
    }

    public function test_get_returns_null_when_claim_not_found(): void
    {
        $this->invoiceService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn(new InvoiceEntity)
        ;

        $this->claimService->expects($this->once())
            ->method('getById')
            ->with(999)
            ->willReturn(null)
        ;

        $result = $this->command->get(1, 999);
        $this->assertNull($result);
    }

    public function test_get_returns_null_when_claim_service_not_found(): void
    {
        $this->invoiceService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn(new InvoiceEntity)
        ;

        $claim = new ClaimEntity;
        $claim->setId(100)->setInvoiceId(1)->setServiceId(10);

        $this->claimService->expects($this->once())
            ->method('getById')
            ->with(100)
            ->willReturn($claim)
        ;

        $this->serviceService->expects($this->once())
            ->method('getById')
            ->with(10)
            ->willReturn(null)
        ;

        $result = $this->command->get(1, 100);
        $this->assertNull($result);
    }
}
