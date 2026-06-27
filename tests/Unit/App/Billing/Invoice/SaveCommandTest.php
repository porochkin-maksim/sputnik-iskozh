<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Invoice;

use Core\App\Billing\Invoice\SaveCommand;
use Core\App\Billing\Invoice\SaveValidator;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceFactory;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Infra\DbLock\Service\LockService;
use Core\Exceptions\ValidationException;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class SaveCommandTest extends TestCase
{
    private InvoiceFactory $invoiceFactory;
    private InvoiceService $invoiceService;
    private SaveValidator  $validator;
    private SaveCommand    $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invoiceFactory = new InvoiceFactory;
        $this->invoiceService = $this->createMock(InvoiceService::class);
        $this->validator      = $this->createMock(SaveValidator::class);

        $this->instance(LockService::class, $this->createMock(LockService::class));

        $this->command = new SaveCommand(
            $this->invoiceFactory,
            $this->invoiceService,
            $this->validator,
        );
    }

    public function test_execute_creates_new_invoice(): void
    {
        Bus::fake();
        $this->validator->expects($this->once())->method('validate');

        $this->invoiceService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(InvoiceEntity $i) => $i->getPeriodId() === 1
                                                           && $i->getAccountId() === 2
                                                           && $i->getName() === 'test',
            ))
            ->willReturnCallback(fn(InvoiceEntity $i) => $i->setId(10))
        ;

        $result = $this->command->execute(null, 1, 2, 1, 'test');

        $this->assertSame(10, $result->getId());
    }

    public function test_execute_updates_existing_invoice(): void
    {
        $existing = new InvoiceEntity;
        $existing->setId(5)->setPeriodId(1)->setAccountId(2)->setName('old');

        $this->validator->expects($this->once())->method('validate');

        $this->invoiceService->expects($this->once())
            ->method('getById')
            ->with(5)
            ->willReturn($existing)
        ;

        $this->invoiceService->expects($this->once())
            ->method('save')
            ->with($this->callback(fn(InvoiceEntity $i) => $i->getName() === 'updated'))
            ->willReturnCallback(fn(InvoiceEntity $i) => $i)
        ;

        $result = $this->command->execute(5, 1, 2, 1, 'updated');

        $this->assertSame(5, $result->getId());
        $this->assertSame('updated', $result->getName());
    }

    public function test_execute_returns_null_when_invoice_not_found(): void
    {
        $this->validator->expects($this->once())->method('validate');

        $this->invoiceService->expects($this->once())
            ->method('getById')
            ->with(999)
            ->willReturn(null)
        ;

        $this->invoiceService->expects($this->never())->method('save');

        $result = $this->command->execute(999, 1, 2, 1, 'test');

        $this->assertNull($result);
    }

    public function test_execute_throws_on_validation_error(): void
    {
        $this->validator->expects($this->once())
            ->method('validate')
            ->willThrowException(new ValidationException(['period_id' => ['error']]))
        ;

        $this->expectException(ValidationException::class);

        $this->command->execute(null, 0, 2, 1, 'test');
    }
}
