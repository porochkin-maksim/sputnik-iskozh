<?php declare(strict_types=1);

namespace Tests\Unit\App\HelpDesk\Ticket;

use Core\App\HelpDesk\Ticket\SendTicketCreatedNotificationInput;
use Tests\TestCase;

class SendTicketCreatedNotificationInputTest extends TestCase
{
    public function test_constructor_sets_properties(): void
    {
        $input = new SendTicketCreatedNotificationInput(ticketId: 42);

        $this->assertSame(42, $input->ticketId);
    }
}
