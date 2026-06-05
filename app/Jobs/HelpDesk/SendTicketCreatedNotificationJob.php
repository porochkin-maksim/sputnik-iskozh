<?php declare(strict_types=1);

namespace App\Jobs\HelpDesk;

use App\Services\Queue\QueueEnum;
use Core\App\HelpDesk\Ticket\SendTicketCreatedNotificationCommand;
use Core\App\HelpDesk\Ticket\SendTicketCreatedNotificationInput;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendTicketCreatedNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $ticketId,
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    public function handle(SendTicketCreatedNotificationCommand $command): void
    {
        $command->execute(new SendTicketCreatedNotificationInput($this->ticketId));
    }
}
