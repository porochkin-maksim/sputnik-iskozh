<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Invoice;

use Core\App\Billing\Invoice\InvoiceImportService;
use Core\Contracts\EventDispatcherInterface;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use Core\Domains\Infra\DbLock\Service\LockService;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class InvoiceImportServiceTest extends TestCase
{
    private InvoiceService           $invoiceService;
    private LockService              $lockService;
    private EventDispatcherInterface $eventDispatcher;
    private InvoiceImportService     $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invoiceService  = $this->createMock(InvoiceService::class);
        $this->lockService     = $this->createMock(LockService::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->service         = new InvoiceImportService(
            $this->invoiceService,
            $this->lockService,
            $this->eventDispatcher,
        );
    }

    public function test_save_payments_throws_when_lock_unavailable(): void
    {
        $this->lockService->method('isAvailable')
            ->with(LockNameEnum::SAVE_IMPORT_PAYMENTS_JOB)
            ->willReturn(false)
        ;

        $this->lockService->expects($this->never())->method('lock');
        $this->eventDispatcher->expects($this->never())->method('dispatch');

        try {
            $this->service->savePayments([]);
            $this->fail('Expected HttpException');
        }
        catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
    }

    public function test_save_payments_dispatches_event(): void
    {
        $this->lockService->method('isAvailable')
            ->with(LockNameEnum::SAVE_IMPORT_PAYMENTS_JOB)
            ->willReturn(true)
        ;

        $this->lockService->expects($this->once())
            ->method('lock')
            ->with(LockNameEnum::SAVE_IMPORT_PAYMENTS_JOB)
        ;

        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
        ;

        $this->service->savePayments([
            ['invoice_id' => 1, 'amount' => 100.0],
        ]);
    }
}
