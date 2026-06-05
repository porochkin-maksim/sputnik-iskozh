<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Acquiring;

use Core\App\Billing\Acquiring\CreatePaymentLinkCommand;
use Core\Domains\Billing\Acquiring\AcquiringEntity;
use Core\Domains\Billing\Acquiring\Enums\StatusEnum;
use Core\Domains\Billing\Acquiring\Services\AcquiringFactory;
use Core\Domains\Billing\Acquiring\Services\AcquiringService;
use Core\Domains\Billing\Acquiring\Services\ProviderGateway;
use Core\Domains\Billing\Acquiring\Services\ProviderSelector;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Tests\TestCase;

class CreatePaymentLinkCommandTest extends TestCase
{
    private InvoiceService           $invoiceService;
    private AcquiringService         $acquiringService;
    private ProviderGateway          $providerGateway;
    private HistoryChangesService    $historyChangesService;
    private CreatePaymentLinkCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invoiceService        = $this->createMock(InvoiceService::class);
        $this->acquiringService      = $this->createMock(AcquiringService::class);
        $this->providerGateway       = $this->createMock(ProviderGateway::class);
        $this->historyChangesService = $this->createMock(HistoryChangesService::class);
        $providerSelector            = $this->createMock(ProviderSelector::class);
        $acquiringFactory            = new AcquiringFactory($providerSelector);

        $this->command = new CreatePaymentLinkCommand(
            $this->invoiceService,
            $this->acquiringService,
            $acquiringFactory,
            $this->providerGateway,
            $this->historyChangesService,
        );
    }

    public function test_execute_creates_new_acquiring_and_returns_link(): void
    {
        $invoice = new InvoiceEntity;
        $invoice->setId(1);

        $this->invoiceService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($invoice)
        ;

        $this->acquiringService->expects($this->once())
            ->method('findForInvoiceUserAndAmount')
            ->with(1, 42, 1000.0)
            ->willReturn(null)
        ;

        $this->acquiringService->expects($this->exactly(2))
            ->method('save')
            ->willReturnCallback(fn(AcquiringEntity $e) => $e)
        ;

        $this->providerGateway->expects($this->once())
            ->method('getPaymentLink')
            ->willReturn('https://payment.link/test')
        ;

        $result = $this->command->execute(1, 1000.0, 42);

        $this->assertSame('https://payment.link/test', $result);
    }

    public function test_execute_reuses_existing_acquiring(): void
    {
        $invoice = new InvoiceEntity();
        $invoice->setId(1);

        $existing = new AcquiringEntity;
        $existing->setId(55)->setStatus(StatusEnum::NEW);

        $this->invoiceService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($invoice)
        ;

        $this->acquiringService->expects($this->once())
            ->method('findForInvoiceUserAndAmount')
            ->with(1, 42, 1000.0)
            ->willReturn($existing)
        ;

        $this->acquiringService->expects($this->once())
            ->method('save')
            ->willReturnCallback(fn(AcquiringEntity $e) => $e)
        ;

        $this->providerGateway->expects($this->once())
            ->method('getPaymentLink')
            ->willReturn('https://payment.link/existing')
        ;

        $result = $this->command->execute(1, 1000.0, 42);

        $this->assertSame('https://payment.link/existing', $result);
    }

    public function test_execute_returns_null_when_invoice_not_found(): void
    {
        $this->invoiceService->expects($this->once())
            ->method('getById')
            ->with(999)
            ->willReturn(null)
        ;

        $result = $this->command->execute(999, 1000.0, 42);

        $this->assertNull($result);
    }

    public function test_execute_returns_null_when_amount_zero(): void
    {
        $invoice = new InvoiceEntity;
        $invoice->setId(1);

        $this->invoiceService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($invoice)
        ;

        $result = $this->command->execute(1, 0, 42);

        $this->assertNull($result);
    }
}
