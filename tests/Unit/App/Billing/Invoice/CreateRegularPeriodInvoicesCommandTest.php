<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Invoice;

use Core\App\Billing\Invoice\CreateRegularPeriodInvoicesCommand;
use Core\App\Billing\Invoice\CreateRegularPeriodInvoicesInput;
use Core\Contracts\EventDispatcherInterface;
use Core\Domains\Account\AccountCollection;
use Core\Domains\Account\AccountEntity;
use Core\Domains\Billing\Events\RegularPeriodInvoiceBatchRequested;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceFactory;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Period\PeriodEntity;
use Core\Domains\Billing\Period\PeriodService;
use RuntimeException;
use Tests\TestCase;

class CreateRegularPeriodInvoicesCommandTest extends TestCase
{
    private PeriodService                      $periodService;
    private InvoiceService                     $invoiceService;
    private InvoiceFactory                     $invoiceFactory;
    private EventDispatcherInterface           $eventDispatcher;
    private CreateRegularPeriodInvoicesCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->periodService   = $this->createMock(PeriodService::class);
        $this->invoiceService  = $this->createMock(InvoiceService::class);
        $this->invoiceFactory  = new InvoiceFactory;
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->command = new CreateRegularPeriodInvoicesCommand(
            $this->periodService,
            $this->invoiceService,
            $this->invoiceFactory,
            $this->eventDispatcher,
        );
    }

    public function test_execute_throws_when_period_not_found(): void
    {
        $this->periodService->method('getById')->with(999)->willReturn(null);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Период не найден #999');

        $this->command->execute(new CreateRegularPeriodInvoicesInput(999));
    }

    public function test_execute_creates_invoices_for_given_accounts(): void
    {
        $period = new PeriodEntity;
        $period->setId(1);

        $this->periodService->method('getById')->with(1)->willReturn($period);

        $this->invoiceService->expects($this->exactly(2))
            ->method('save')
            ->with($this->callback(fn(InvoiceEntity $i) => $i->getPeriodId() === 1
                                                           && in_array($i->getAccountId(), [10, 20], true),
            ))
        ;

        $this->eventDispatcher->expects($this->never())->method('dispatch');

        $this->command->execute(new CreateRegularPeriodInvoicesInput(1, [10, 20]));
    }

    public function test_execute_dispatches_batches_when_no_account_ids(): void
    {
        $period = new PeriodEntity;
        $period->setId(1);

        $this->periodService->method('getById')->with(1)->willReturn($period);

        $accountCollection = new AccountCollection;
        $account1          = new AccountEntity;
        $account1->setId(10);
        $account2 = new AccountEntity;
        $account2->setId(20);
        $accountCollection->add($account1);
        $accountCollection->add($account2);

        $this->invoiceService->method('getAccountsWithoutRegularInvoice')
            ->with(1)
            ->willReturn($accountCollection)
        ;

        $this->invoiceService->expects($this->never())->method('save');

        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(fn(array $events) => count($events) === 1
                                                        && $events[0] instanceof RegularPeriodInvoiceBatchRequested
                                                        && $events[0]->periodId === 1
                                                        && $events[0]->accountIds === [10, 20],
            ))
        ;

        $this->command->execute(new CreateRegularPeriodInvoicesInput(1));
    }

    public function test_execute_does_not_dispatch_when_no_accounts_without_invoice(): void
    {
        $period = new PeriodEntity;
        $period->setId(1);

        $this->periodService->method('getById')->with(1)->willReturn($period);

        $this->invoiceService->method('getAccountsWithoutRegularInvoice')
            ->with(1)
            ->willReturn(new AccountCollection)
        ;

        $this->eventDispatcher->expects($this->never())->method('dispatch');

        $this->command->execute(new CreateRegularPeriodInvoicesInput(1));
    }
}
