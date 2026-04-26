<?php declare(strict_types=1);

namespace Core\Domains\HelpDesk\Jobs;

use Core\Domains\Access\PermissionEnum;
use Core\Domains\Access\RoleService;
use Core\Domains\HelpDesk\Mails\NewTicketCreatedEmail;
use Core\Domains\HelpDesk\Services\TicketService;
use Core\Domains\HistoryChanges\Event;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Core\Domains\HistoryChanges\HistoryType;
use App\Services\Queue\QueueEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifyAboutNewTicketJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $ticketId,
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    public function handle(
        TicketService $ticketService,
        RoleService $roleService,
        HistoryChangesService $historyChangesService,
    ): void
    {
        $ticket = $ticketService->getById($this->ticketId);

        if ( ! $ticket || ! $ticket->getStatus()?->isNew()) {
            return;
        }

        $emails = $roleService->getEmailsByPermissions(PermissionEnum::PAYMENTS_EDIT);
        $emails = array_unique(array_merge($emails, [config('mail.emails.admin')]));

        foreach ($emails as $email) {
            $mail = new NewTicketCreatedEmail(
                $email,
                $ticket,
            );
            Mail::send($mail);

            $historyChangesService->writeToHistory(
                Event::COMMON,
                HistoryType::TICKET,
                $ticket->getId(),
                text: 'Отправлено уведомление о новой заявке на почту ' . $email,
            );
        }
    }
}
