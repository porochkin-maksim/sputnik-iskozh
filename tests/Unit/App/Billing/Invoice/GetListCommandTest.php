<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Invoice;

use App\Models\Billing\Invoice;
use Core\App\Billing\Invoice\GetListCommand;
use Core\Domains\Account\AccountCollection;
use Core\Domains\Account\AccountEntity;
use Core\Domains\Account\AccountSearcher;
use Core\Domains\Account\AccountSearchResponse;
use Core\Domains\Account\AccountService;
use Core\Domains\Billing\Invoice\InvoiceCollection;
use Core\Domains\Billing\Invoice\InvoiceSearcher;
use Core\Domains\Billing\Invoice\InvoiceSearchResponse;
use Core\Domains\Billing\Invoice\InvoiceService;
use Tests\TestCase;

class GetListCommandTest extends TestCase
{
    private InvoiceService $invoiceService;
    private AccountService $accountService;
    private GetListCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invoiceService = $this->createMock(InvoiceService::class);
        $this->accountService = $this->createMock(AccountService::class);

        $this->command = new GetListCommand(
            $this->invoiceService,
            $this->accountService,
        );
    }

    public function test_execute_with_default_sort(): void
    {
        $response = new InvoiceSearchResponse;
        $response->setItems(new InvoiceCollection);

        $this->invoiceService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(InvoiceSearcher::class))
            ->willReturn($response)
        ;

        $result = $this->command->execute(10, 0);

        $this->assertSame($response, $result);
    }

    public function test_execute_with_asc_sort(): void
    {
        $response = new InvoiceSearchResponse;
        $response->setItems(new InvoiceCollection);

        $this->invoiceService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(InvoiceSearcher::class))
            ->willReturn($response)
        ;

        $result = $this->command->execute(10, 0, Invoice::COST, 'asc');

        $this->assertSame($response, $result);
    }

    public function test_execute_filters_by_paid_status(): void
    {
        $response = new InvoiceSearchResponse;
        $response->setItems(new InvoiceCollection);

        $this->invoiceService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(InvoiceSearcher::class))
            ->willReturn($response)
        ;

        $result = $this->command->execute(10, 0, null, null, 'unpaid');

        $this->assertSame($response, $result);
    }

    public function test_execute_filters_by_partial_status(): void
    {
        $response = new InvoiceSearchResponse;
        $response->setItems(new InvoiceCollection);

        $this->invoiceService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(InvoiceSearcher::class))
            ->willReturn($response)
        ;

        $result = $this->command->execute(10, 0, null, null, 'partial');

        $this->assertSame($response, $result);
    }

    public function test_execute_with_account_search(): void
    {
        $accountEntity     = new AccountEntity;
        $accountCollection = new AccountCollection([$accountEntity]);

        $accountResponse = new AccountSearchResponse;
        $accountResponse->setItems($accountCollection);

        $this->accountService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(AccountSearcher::class))
            ->willReturn($accountResponse)
        ;

        $response = new InvoiceSearchResponse;
        $response->setItems(new InvoiceCollection);

        $this->invoiceService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(InvoiceSearcher::class))
            ->willReturn($response)
        ;

        $result = $this->command->execute(10, 0, null, null, null, null, '15');

        $this->assertSame($response, $result);
    }

    public function test_execute_with_type_and_period(): void
    {
        $response = new InvoiceSearchResponse;
        $response->setItems(new InvoiceCollection);

        $this->invoiceService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(InvoiceSearcher::class))
            ->willReturn($response)
        ;

        $result = $this->command->execute(10, 0, null, null, null, 3, null, 1);

        $this->assertSame($response, $result);
    }

    public function test_execute_with_paid_status_paid(): void
    {
        $response = new InvoiceSearchResponse;
        $response->setItems(new InvoiceCollection);

        $this->invoiceService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(InvoiceSearcher::class))
            ->willReturn($response)
        ;

        $result = $this->command->execute(10, 0, null, null, 'paid');

        $this->assertSame($response, $result);
    }

    public function test_execute_with_account_search_no_results(): void
    {
        $accountResponse = new AccountSearchResponse;
        $accountResponse->setItems(new AccountCollection);

        $this->accountService->expects($this->once())
            ->method('search')
            ->willReturn($accountResponse)
        ;

        $response = new InvoiceSearchResponse;
        $response->setItems(new InvoiceCollection);

        $this->invoiceService->expects($this->once())
            ->method('search')
            ->willReturn($response)
        ;

        $result = $this->command->execute(10, 0, null, null, null, null, 'non-existent');

        $this->assertSame($response, $result);
    }
}
