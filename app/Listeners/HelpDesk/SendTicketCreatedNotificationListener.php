<?php declare(strict_types=1);

namespace App\Listeners\HelpDesk;

use App\Jobs\HelpDesk\SendTicketCreatedNotificationJob;
use Core\Domains\HelpDesk\Events\TicketCreated;

class SendTicketCreatedNotificationListener
{
    public function handle(TicketCreated $event): void
    {
        dispatch(new SendTicketCreatedNotificationJob($event->ticketId));
    }
}
