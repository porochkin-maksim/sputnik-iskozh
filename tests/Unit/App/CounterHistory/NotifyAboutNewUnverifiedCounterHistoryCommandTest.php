<?php declare(strict_types=1);

namespace Tests\Unit\App\CounterHistory;

use Carbon\Carbon;
use Core\App\CounterHistory\NotifyAboutNewUnverifiedCounterHistoryCommand;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Access\RoleService;
use Core\Domains\Account\AccountService;
use Core\Domains\Counter\CounterEntity;
use Core\Domains\Counter\CounterSearchResponse;
use Core\Domains\Counter\CounterService;
use Core\Domains\CounterHistory\CounterHistoryCollection;
use Core\Domains\CounterHistory\CounterHistoryEntity;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class NotifyAboutNewUnverifiedCounterHistoryCommandTest extends TestCase
{
    private CounterHistoryService                         $counterHistoryService;
    private CounterService                                $counterService;
    private AccountService                                $accountService;
    private RoleService                                   $roleService;
    private HistoryChangesService                         $historyChangesService;
    private Mailer                                        $mailer;
    private NotifyAboutNewUnverifiedCounterHistoryCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        Config::shouldReceive('get')
            ->with('mail.emails.admin', null)
            ->andReturn('admin@snt.ru')
        ;

        $this->counterHistoryService = $this->createMock(CounterHistoryService::class);
        $this->counterService        = $this->createMock(CounterService::class);
        $this->accountService        = $this->createMock(AccountService::class);
        $this->roleService           = $this->createMock(RoleService::class);
        $this->historyChangesService = $this->createMock(HistoryChangesService::class);
        $this->mailer                = $this->createMock(Mailer::class);

        $this->command = new NotifyAboutNewUnverifiedCounterHistoryCommand(
            $this->counterHistoryService,
            $this->counterService,
            $this->accountService,
            $this->roleService,
            $this->historyChangesService,
            $this->mailer,
        );
    }

    protected function tearDown(): void
    {
        Config::clearResolvedInstances();
        parent::tearDown();
    }

    public function test_execute_returns_early_when_history_not_found(): void
    {
        $this->counterHistoryService->method('getById')->willReturn(null);

        $this->mailer->expects($this->never())->method('send');

        $this->command->execute(999);
    }

    public function test_execute_returns_early_when_already_verified(): void
    {
        $history = (new CounterHistoryEntity)->setIsVerified(true);
        $this->counterHistoryService->method('getById')->willReturn($history);

        $this->mailer->expects($this->never())->method('send');

        $this->command->execute(1);
    }

    public function test_execute_returns_early_when_same_value_as_previous(): void
    {
        $history = (new CounterHistoryEntity)
            ->setId(5)
            ->setCounterId(2)
            ->setValue(100)
            ->setIsVerified(false)
        ;

        $this->counterHistoryService->method('getById')->willReturn($history);

        $counter = new CounterEntity;
        $counter->setId(2)->setAccountId(10);

        $response = new CounterSearchResponse;
        $response->setItems(new CounterHistoryCollection([$counter]));
        $this->counterService->method('search')->willReturn($response);

        $previous = (new CounterHistoryEntity)->setValue(100);
        $this->counterHistoryService->method('getPrevious')->willReturn($previous);

        $this->mailer->expects($this->never())->method('send');

        $this->command->execute(5);
    }

    public function test_execute_sends_emails(): void
    {
        $history = (new CounterHistoryEntity)
            ->setId(5)
            ->setCounterId(2)
            ->setValue(150)
            ->setIsVerified(false)
            ->setDate(Carbon::now())
        ;

        $this->counterHistoryService->method('getById')->willReturn($history);

        $counter = new CounterEntity;
        $counter->setId(2)->setAccountId(10);

        $response = new CounterSearchResponse;
        $response->setItems(new CounterHistoryCollection([$counter]));
        $this->counterService->method('search')->willReturn($response);

        $previous = (new CounterHistoryEntity)->setValue(100);
        $this->counterHistoryService->method('getPrevious')->willReturn($previous);

        $this->roleService->method('getEmailsByPermissions')
            ->with(PermissionEnum::COUNTERS_EDIT)
            ->willReturn(['admin@example.com'])
        ;

        $this->mailer->expects($this->exactly(2))
            ->method('send')
        ;

        $this->historyChangesService->expects($this->exactly(2))
            ->method('writeToHistory')
        ;

        $this->command->execute(5);
    }

    public function test_execute_sends_to_multiple_emails(): void
    {
        $history = (new CounterHistoryEntity)
            ->setId(5)
            ->setCounterId(2)
            ->setValue(150)
            ->setIsVerified(false)
            ->setDate(Carbon::now())
        ;

        $this->counterHistoryService->method('getById')->willReturn($history);

        $counter = new CounterEntity;
        $counter->setId(2)->setAccountId(10);

        $response = new CounterSearchResponse;
        $response->setItems(new CounterHistoryCollection([$counter]));
        $this->counterService->method('search')->willReturn($response);

        $previous = (new CounterHistoryEntity)->setValue(100);
        $this->counterHistoryService->method('getPrevious')->willReturn($previous);

        $this->roleService->method('getEmailsByPermissions')
            ->willReturn(['admin@example.com', 'moderator@example.com'])
        ;

        $this->mailer->expects($this->exactly(3))
            ->method('send')
        ;

        $this->historyChangesService->expects($this->exactly(3))
            ->method('writeToHistory')
        ;

        $this->command->execute(5);
    }
}
