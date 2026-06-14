<?php declare(strict_types=1);

namespace App\Http\Resources\Profile\HelpDesk;

use App\Http\Resources\AbstractResource;
use Core\Domains\HelpDesk\Collection\TicketCollection;

readonly class TicketListResource extends AbstractResource
{
    public function __construct(
        private TicketCollection $tickets,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $result = [];

        foreach ($this->tickets as $ticket) {
            $result[] = new TicketResource($ticket);
        }

        return $result;
    }
}
