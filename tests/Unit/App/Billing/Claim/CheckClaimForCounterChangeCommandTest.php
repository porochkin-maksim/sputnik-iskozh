<?php declare(strict_types=1);

namespace Tests\Unit\App\Billing\Claim;

use Core\App\Billing\Claim\CheckClaimForCounterChangeCommand;
use Core\App\Billing\Claim\CheckClaimForCounterChangeInput;
use Core\Domains\Account\AccountEntity;
use Core\Domains\Account\AccountService;
use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\Billing\Claim\ClaimFactory;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\ClaimToObject\ClaimObjectTypeEnum;
use Core\Domains\Billing\ClaimToObject\ClaimToObjectService;
use Core\Domains\Billing\Invoice\InvoiceCollection;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceFactory;
use Core\Domains\Billing\Invoice\InvoiceSearcher;
use Core\Domains\Billing\Invoice\InvoiceSearchResponse;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Period\PeriodEntity;
use Core\Domains\Billing\Period\PeriodService;
use Core\Domains\Billing\Service\ServiceCatalogService;
use Core\Domains\Billing\Service\ServiceCollection;
use Core\Domains\Billing\Service\ServiceEntity;
use Core\Domains\Billing\Service\ServiceSearcher;
use Core\Domains\Billing\Service\ServiceSearchResponse;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use Core\Domains\Counter\CounterCollection;
use Core\Domains\Counter\CounterEntity;
use Core\Domains\Counter\CounterSearcher;
use Core\Domains\Counter\CounterSearchResponse;
use Core\Domains\Counter\CounterService;
use Core\Domains\CounterHistory\CounterHistoryEntity;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Domains\HistoryChanges\Event;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Core\Domains\HistoryChanges\HistoryType;
use Tests\TestCase;

class CheckClaimForCounterChangeCommandTest extends TestCase
{
    private ClaimToObjectService              $claimToObjectService;
    private CounterHistoryService             $counterHistoryService;
    private CounterService                    $counterService;
    private PeriodService                     $periodService;
    private ServiceCatalogService             $serviceService;
    private AccountService                    $accountService;
    private InvoiceService                    $invoiceService;
    private InvoiceFactory                    $invoiceFactory;
    private ClaimFactory                      $claimFactory;
    private ClaimService                      $claimService;
    private HistoryChangesService             $historyChangesService;
    private CheckClaimForCounterChangeCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->claimToObjectService  = $this->createMock(ClaimToObjectService::class);
        $this->counterHistoryService = $this->createMock(CounterHistoryService::class);
        $this->counterService        = $this->createMock(CounterService::class);
        $this->periodService         = $this->createMock(PeriodService::class);
        $this->serviceService        = $this->createMock(ServiceCatalogService::class);
        $this->accountService        = $this->createMock(AccountService::class);
        $this->invoiceService        = $this->createMock(InvoiceService::class);
        $this->invoiceFactory        = new InvoiceFactory;
        $this->claimFactory          = new ClaimFactory;
        $this->claimService          = $this->createMock(ClaimService::class);
        $this->historyChangesService = $this->createMock(HistoryChangesService::class);

        $this->command = new CheckClaimForCounterChangeCommand(
            $this->claimToObjectService,
            $this->counterHistoryService,
            $this->counterService,
            $this->periodService,
            $this->serviceService,
            $this->accountService,
            $this->invoiceService,
            $this->invoiceFactory,
            $this->claimFactory,
            $this->claimService,
            $this->historyChangesService,
        );
    }

    public function test_execute_deletes_claim_when_history_not_found(): void
    {
        $claim = new ClaimEntity;
        $claim->setId(5)->setInvoiceId(10);

        $this->claimToObjectService->expects($this->once())
            ->method('getByReference')
            ->with(ClaimObjectTypeEnum::COUNTER_HISTORY, 1)
            ->willReturn($claim)
        ;

        $this->counterHistoryService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn(null)
        ;

        $this->claimService->expects($this->once())
            ->method('deleteById')
            ->with(5)
        ;

        $this->invoiceService->expects($this->once())
            ->method('recalcInvoice')
            ->with(10, true)
        ;

        $this->command->execute(new CheckClaimForCounterChangeInput(1));
    }

    public function test_execute_deletes_claim_when_counter_not_found(): void
    {
        $claim = new ClaimEntity;
        $claim->setId(5)->setInvoiceId(10);

        $this->claimToObjectService->expects($this->once())
            ->method('getByReference')
            ->with(ClaimObjectTypeEnum::COUNTER_HISTORY, 1)
            ->willReturn($claim)
        ;

        $history = new CounterHistoryEntity;
        $history->setCounterId(100);

        $this->counterHistoryService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($history)
        ;

        $counterResponse = new CounterSearchResponse;
        $counterResponse->setItems(new CounterCollection);
        $this->counterService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(CounterSearcher::class))
            ->willReturn($counterResponse)
        ;

        $this->claimService->expects($this->once())
            ->method('deleteById')
            ->with(5)
        ;

        $this->invoiceService->expects($this->once())
            ->method('recalcInvoice')
            ->with(10, true)
        ;

        $this->command->execute(new CheckClaimForCounterChangeInput(1));
    }

    public function test_execute_creates_new_claim_on_happy_path(): void
    {
        $this->claimToObjectService->expects($this->once())
            ->method('getByReference')
            ->with(ClaimObjectTypeEnum::COUNTER_HISTORY, 1)
            ->willReturn(null)
        ;

        $history = new CounterHistoryEntity;
        $history->setId(1)->setCounterId(100)->setValue(150.0)->setDate(now());

        $this->counterHistoryService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($history)
        ;

        $counter = new CounterEntity;
        $counter->setId(100)->setAccountId(200)->setNumber('CT-001');

        $counterResponse = new CounterSearchResponse;
        $counterResponse->setItems(new CounterCollection([$counter]));
        $this->counterService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(CounterSearcher::class))
            ->willReturn($counterResponse)
        ;

        $previousHistory = new CounterHistoryEntity;
        $previousHistory->setValue(100.0)->setIsVerified(true);

        $this->counterHistoryService->expects($this->once())
            ->method('getPrevious')
            ->with($history)
            ->willReturn($previousHistory)
        ;

        $period = new PeriodEntity;
        $period->setId(5);
        $this->periodService->expects($this->once())
            ->method('getActive')
            ->willReturn($period)
        ;

        $tariffService = new ServiceEntity;
        $tariffService->setId(30)->setCost(4.0)->setType(ServiceTypeEnum::ELECTRIC_TARIFF);

        $serviceResponse = new ServiceSearchResponse;
        $serviceResponse->setItems(new ServiceCollection([$tariffService]));
        $this->serviceService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(ServiceSearcher::class))
            ->willReturn($serviceResponse)
        ;

        $account = new AccountEntity;
        $account->setId(200)->setNumber('15/1');
        $this->accountService->expects($this->once())
            ->method('getById')
            ->with(200)
            ->willReturn($account)
        ;

        $linkingInvoice = new InvoiceEntity;
        $linkingInvoice->setId(50);

        $invoiceResponse = new InvoiceSearchResponse;
        $invoiceResponse->setItems(new InvoiceCollection);
        $this->invoiceService->expects($this->once())
            ->method('search')
            ->with($this->isInstanceOf(InvoiceSearcher::class))
            ->willReturn($invoiceResponse)
        ;

        $this->invoiceService->expects($this->once())
            ->method('save')
            ->willReturn($linkingInvoice)
        ;

        $this->claimService->expects($this->once())
            ->method('save')
            ->willReturnCallback(fn(ClaimEntity $c) => $c->setId(70))
        ;

        $this->invoiceService->expects($this->once())
            ->method('recalcInvoice')
            ->with(50, true)
        ;

        $this->historyChangesService->expects($this->once())
            ->method('writeToHistory')
            ->with(
                Event::COMMON,
                HistoryType::INVOICE,
                50,
                HistoryType::CLAIM,
                70,
                null,
                null,
                $this->stringContains('Создана'),
            )
        ;

        $this->claimToObjectService->expects($this->once())
            ->method('hasRelations')
            ->with($this->isInstanceOf(ClaimEntity::class))
            ->willReturn(false)
        ;

        $this->claimToObjectService->expects($this->once())
            ->method('create')
            ->with(
                $this->isInstanceOf(ClaimEntity::class),
                1,
                ClaimObjectTypeEnum::COUNTER_HISTORY,
            )
        ;

        $this->command->execute(new CheckClaimForCounterChangeInput(1));
    }

    public function test_execute_updates_existing_claim(): void
    {
        $claim = new ClaimEntity;
        $claim->setId(5)->setInvoiceId(50);

        $this->claimToObjectService->expects($this->once())
            ->method('getByReference')
            ->with(ClaimObjectTypeEnum::COUNTER_HISTORY, 1)
            ->willReturn($claim)
        ;

        $history = new CounterHistoryEntity;
        $history->setCounterId(100)->setValue(150.0)->setDate(now());

        $this->counterHistoryService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($history)
        ;

        $counter = new CounterEntity;
        $counter->setId(100)->setAccountId(200)->setNumber('CT-001');

        $counterResponse = new CounterSearchResponse;
        $counterResponse->setItems(new CounterCollection([$counter]));
        $this->counterService->expects($this->once())
            ->method('search')
            ->willReturn($counterResponse)
        ;

        $previousHistory = new CounterHistoryEntity;
        $previousHistory->setValue(100.0)->setIsVerified(true);

        $this->counterHistoryService->expects($this->once())
            ->method('getPrevious')
            ->willReturn($previousHistory)
        ;

        $period = new PeriodEntity;
        $period->setId(5);
        $this->periodService->expects($this->once())
            ->method('getActive')
            ->willReturn($period)
        ;

        $tariffService = new ServiceEntity;
        $tariffService->setId(30)->setCost(4.0)->setType(ServiceTypeEnum::ELECTRIC_TARIFF);

        $serviceResponse = new ServiceSearchResponse;
        $serviceResponse->setItems(new ServiceCollection([$tariffService]));
        $this->serviceService->expects($this->once())
            ->method('search')
            ->willReturn($serviceResponse)
        ;

        $account = new AccountEntity;
        $account->setId(200)->setNumber('15/1');
        $this->accountService->expects($this->once())
            ->method('getById')
            ->willReturn($account)
        ;

        $linkingInvoice = new InvoiceEntity;
        $linkingInvoice->setId(50);

        $invoiceResponse = new InvoiceSearchResponse;
        $invoiceResponse->setItems(new InvoiceCollection([$linkingInvoice]));

        $this->invoiceService->expects($this->once())
            ->method('search')
            ->willReturn($invoiceResponse)
        ;

        $this->invoiceService->expects($this->never())->method('save');

        $this->claimService->expects($this->once())
            ->method('save')
            ->willReturnCallback(fn(ClaimEntity $c) => $c)
        ;

        $this->invoiceService->expects($this->once())
            ->method('recalcInvoice')
        ;

        $this->historyChangesService->expects($this->once())
            ->method('writeToHistory')
            ->with(
                Event::COMMON,
                HistoryType::INVOICE,
                50,
                HistoryType::CLAIM,
                5,
                null,
                null,
                $this->stringContains('Обновлена'),
            )
        ;

        $this->claimToObjectService->expects($this->once())
            ->method('hasRelations')
            ->willReturn(true)
        ;

        $this->claimToObjectService->expects($this->never())->method('create');

        $this->command->execute(new CheckClaimForCounterChangeInput(1));
    }
}
