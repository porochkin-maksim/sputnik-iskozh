<?php declare(strict_types=1);

namespace Tests\Unit\App\HelpDesk\Ticket;

use Core\App\HelpDesk\Ticket\SendTicketCreatedNotificationCommand;
use Core\App\HelpDesk\Ticket\SendTicketCreatedNotificationInput;
use Core\Contracts\MailSenderInterface;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Access\RoleService;
use Core\Domains\HelpDesk\Enums\TicketStatusEnum;
use Core\Domains\HelpDesk\Mails\NewTicketCreatedEmail;
use Core\Domains\HelpDesk\Models\TicketEntity;
use Core\Domains\HelpDesk\Services\TicketService;
use Core\Domains\HistoryChanges\Event;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Core\Domains\HistoryChanges\HistoryType;
use Tests\TestCase;

class SendTicketCreatedNotificationCommandTest extends TestCase
{
    private TicketService                        $ticketService;
    private RoleService                          $roleService;
    private HistoryChangesService                $historyChangesService;
    private MailSenderInterface                  $mailSender;
    private SendTicketCreatedNotificationCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ticketService         = $this->createMock(TicketService::class);
        $this->roleService           = $this->createMock(RoleService::class);
        $this->historyChangesService = $this->createMock(HistoryChangesService::class);
        $this->mailSender            = $this->createMock(MailSenderInterface::class);

        $this->command = new SendTicketCreatedNotificationCommand(
            $this->ticketService,
            $this->roleService,
            $this->historyChangesService,
            $this->mailSender,
        );
    }

    public function test_execute_returns_early_when_ticket_not_found(): void
    {
        $this->ticketService->expects($this->once())
            ->method('getById')
            ->with(999)
            ->willReturn(null)
        ;

        $this->roleService->expects($this->never())->method('getEmailsByPermissions');

        $this->command->execute(new SendTicketCreatedNotificationInput(999));
    }

    public function test_execute_returns_early_when_ticket_not_new(): void
    {
        $ticket = (new TicketEntity)->setId(1)->setStatus(TicketStatusEnum::IN_PROGRESS);

        $this->ticketService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($ticket)
        ;

        $this->roleService->expects($this->never())->method('getEmailsByPermissions');

        $this->command->execute(new SendTicketCreatedNotificationInput(1));
    }

    public function test_execute_sends_notification_to_admins(): void
    {
        $ticket = (new TicketEntity)->setId(1)->setStatus(TicketStatusEnum::NEW);

        $this->ticketService->expects($this->once())
            ->method('getById')
            ->with(1)
            ->willReturn($ticket)
        ;

        $this->roleService->expects($this->once())
            ->method('getEmailsByPermissions')
            ->with(PermissionEnum::PAYMENTS_EDIT)
            ->willReturn(['admin1@test.com', 'admin2@test.com'])
        ;

        $this->mailSender->expects($this->exactly(3))
            ->method('send')
            ->with($this->isInstanceOf(NewTicketCreatedEmail::class))
        ;

        $this->historyChangesService->expects($this->exactly(3))
            ->method('writeToHistory')
            ->with(
                Event::COMMON,
                HistoryType::TICKET,
                1,
                $this->anything(),
                $this->anything(),
                $this->anything(),
                $this->anything(),
                $this->stringContains('Отправлено уведомление'),
            )
        ;

        $this->command->execute(new SendTicketCreatedNotificationInput(1));
    }
}
