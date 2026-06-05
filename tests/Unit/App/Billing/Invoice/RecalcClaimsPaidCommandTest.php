<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Invoice;

use Core\App\Billing\Invoice\RecalcClaimsPaidCommand;
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
use Core\Domains\Billing\Payment\PaymentCollection;
use Core\Domains\Billing\Payment\PaymentEntity;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceCollection;
use Core\Domains\Billing\Service\ServiceEntity;
use Core\Domains\Billing\Service\ServiceSearcher;
use Core\Domains\Billing\Service\ServiceSearchResponse;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use Tests\TestCase;

class RecalcClaimsPaidCommandTest extends TestCase
{
    private InvoiceService          $invoiceService;
    private ClaimService            $claimService;
    private ServiceCatalogService   $serviceService;
    private ClaimFactory            $claimFactory;
    private RecalcClaimsPaidCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invoiceService = $this->createMock(InvoiceService::class);
        $this->claimService   = $this->createMock(ClaimService::class);
        $this->serviceService = $this->createMock(ServiceCatalogService::class);
        $this->claimFactory   = new ClaimFactory;

        $this->command = new RecalcClaimsPaidCommand(
            $this->invoiceService,
            $this->claimService,
            $this->serviceService,
            $this->claimFactory,
        );
    }

    public function test_execute_returns_early_when_invoice_not_found(): void
    {
        $response = new InvoiceSearchResponse;
        $response->setItems(new InvoiceCollection);

        $this->invoiceService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(InvoiceSearcher::class))
            ->willReturn($response)
        ;

        $this->claimService->expects($this->never())->method('search');
        $this->invoiceService->expects($this->never())->method('save');

        $this->command->execute(999);
    }

    public function test_execute_fully_paid_without_advance(): void
    {
        $invoice = new InvoiceEntity;
        $invoice->setId(1)->setPeriodId(5);

        $payment = new PaymentEntity;
        $payment->setVerified(true)->setCost(130.0);
        $invoice->setPayments(new PaymentCollection([$payment]));

        $invoiceResponse = new InvoiceSearchResponse;
        $invoiceResponse->setItems(new InvoiceCollection([$invoice]));

        $this->invoiceService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(InvoiceSearcher::class))
            ->willReturn($invoiceResponse)
        ;

        $membershipService = new ServiceEntity;
        $membershipService->setType(ServiceTypeEnum::MEMBERSHIP_FEE);

        $targetService = new ServiceEntity;
        $targetService->setType(ServiceTypeEnum::TARGET_FEE);

        $claim1 = new ClaimEntity;
        $claim1->setId(1)->setCost(50.0)->setService($membershipService);
        $claim2 = new ClaimEntity;
        $claim2->setId(2)->setCost(80.0)->setService($targetService);

        $claimResponse = new ClaimSearchResponse;
        $claimResponse->setItems(new ClaimCollection([$claim1, $claim2]));

        $this->claimService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(ClaimSearcher::class))
            ->willReturn($claimResponse)
        ;

        $this->serviceService->expects($this->never())->method('search');

        $this->claimService->expects($this->once())
            ->method('saveCollection')
            ->willReturnCallback(fn(ClaimCollection $c) => $c)
        ;

        $this->invoiceService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(InvoiceEntity $i) => $i->getCost() === 130.0
                                                           && $i->getPaid() === 130.0,
            ))
        ;

        $this->command->execute(1);
    }

    public function test_execute_creates_advance_on_overpayment(): void
    {
        $invoice = new InvoiceEntity;
        $invoice->setId(1)->setPeriodId(5);

        $payment = new PaymentEntity;
        $payment->setVerified(true)->setCost(200.0);
        $invoice->setPayments(new PaymentCollection([$payment]));

        $invoiceResponse = new InvoiceSearchResponse;
        $invoiceResponse->setItems(new InvoiceCollection([$invoice]));

        $this->invoiceService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(InvoiceSearcher::class))
            ->willReturn($invoiceResponse)
        ;

        $membershipService = new ServiceEntity;
        $membershipService->setType(ServiceTypeEnum::MEMBERSHIP_FEE);

        $claim1 = new ClaimEntity;
        $claim1->setId(1)->setCost(50.0)->setService($membershipService);

        $claimResponse = new ClaimSearchResponse;
        $claimResponse->setItems(new ClaimCollection([$claim1]));

        $this->claimService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(ClaimSearcher::class))
            ->willReturn($claimResponse)
        ;

        $advanceService = new ServiceEntity;
        $advanceService->setId(10)->setType(ServiceTypeEnum::ADVANCE_PAYMENT);

        $serviceResponse = new ServiceSearchResponse;
        $serviceResponse->setItems(new ServiceCollection([$advanceService]));

        $this->serviceService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(ServiceSearcher::class))
            ->willReturn($serviceResponse)
        ;

        $this->claimService->expects($this->once())
            ->method('saveCollection')
            ->willReturnCallback(fn(ClaimCollection $c) => $c)
        ;

        $this->invoiceService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(InvoiceEntity $i) => $i->getAdvance() === 150.0
                                                           && $i->getPaid() === 200.0,
            ))
        ;

        $this->command->execute(1);
    }

    public function test_execute_removes_advance_when_no_remaining(): void
    {
        $invoice = new InvoiceEntity;
        $invoice->setId(1)->setPeriodId(5);

        $payment = new PaymentEntity;
        $payment->setVerified(true)->setCost(50.0);
        $invoice->setPayments(new PaymentCollection([$payment]));

        $invoiceResponse = new InvoiceSearchResponse;
        $invoiceResponse->setItems(new InvoiceCollection([$invoice]));

        $this->invoiceService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(InvoiceSearcher::class))
            ->willReturn($invoiceResponse)
        ;

        $advanceServiceType = new ServiceEntity;
        $advanceServiceType->setType(ServiceTypeEnum::ADVANCE_PAYMENT);

        $advanceClaim = new ClaimEntity;
        $advanceClaim->setId(3)->setCost(20.0)->setPaid(20.0)->setService($advanceServiceType);

        $membershipService = new ServiceEntity;
        $membershipService->setType(ServiceTypeEnum::MEMBERSHIP_FEE);

        $claim1 = new ClaimEntity;
        $claim1->setId(1)->setCost(50.0)->setService($membershipService);

        $claimResponse = new ClaimSearchResponse;
        $claimResponse->setItems(new ClaimCollection([$advanceClaim, $claim1]));

        $this->claimService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(ClaimSearcher::class))
            ->willReturn($claimResponse)
        ;

        $this->serviceService->expects($this->never())->method('search');

        $this->claimService->expects($this->never())->method('deleteById');

        $this->claimService->expects($this->once())
            ->method('saveCollection')
            ->willReturnCallback(fn(ClaimCollection $c) => $c)
        ;

        $this->invoiceService->expects($this->once())
            ->method('save')
        ;

        $this->command->execute(1);
    }
}
