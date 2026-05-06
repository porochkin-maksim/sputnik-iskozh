<?php declare(strict_types=1);

namespace Core\App\HelpDesk\Ticket;

use Core\Contracts\MailSenderInterface;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Access\RoleService;
use Core\Domains\HelpDesk\Mails\NewTicketCreatedEmail;
use Core\Domains\HelpDesk\Services\TicketService;
use Core\Domains\HistoryChanges\Event;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Core\Domains\HistoryChanges\HistoryType;

readonly class SendTicketCreatedNotificationHandler
{
    public function __construct(
        private TicketService         $ticketService,
        private RoleService           $roleService,
        private HistoryChangesService $historyChangesService,
        private MailSenderInterface   $mailSender,
    )
    {
    }

    public function handle(SendTicketCreatedNotificationCommand $command): void
    {
        $ticket = $this->ticketService->getById($command->ticketId);

        if ( ! $ticket || ! $ticket->getStatus()?->isNew()) {
            return;
        }

        $emails = $this->roleService->getEmailsByPermissions(PermissionEnum::PAYMENTS_EDIT);
        $emails = array_unique(array_merge($emails, [config('mail.emails.admin')]));

        foreach ($emails as $email) {
            $this->mailSender->send(new NewTicketCreatedEmail($email, $ticket));

            $this->historyChangesService->writeToHistory(
                Event::COMMON,
                HistoryType::TICKET,
                $ticket->getId(),
                text: 'Отправлено уведомление о новой заявке на почту ' . $email,
            );
        }
    }
}
