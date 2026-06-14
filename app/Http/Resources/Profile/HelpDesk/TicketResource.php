<?php declare(strict_types=1);

namespace App\Http\Resources\Profile\HelpDesk;

use App\Http\Resources\AbstractResource;
use Core\Domains\HelpDesk\Models\TicketEntity;

readonly class TicketResource extends AbstractResource
{
    public function __construct(
        private TicketEntity $ticket,
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'id'            => $this->ticket->getId(),
            'type_name'     => $this->ticket->getType()?->name(),
            'type_icon'     => $this->ticket->getType()?->icon(),
            'type_color'    => $this->ticket->getType()?->color(),
            'category_name' => $this->ticket->getCategory()?->getName(),
            'service_name'  => $this->ticket->getService()?->getName(),
            'status'        => $this->ticket->getStatus()?->value,
            'status_name'   => $this->ticket->getStatus()?->name(),
            'description'   => $this->ticket->getDescription(),
            'result'        => $this->ticket->getResult(),
            'created_at'    => $this->formatDateTimeForRender($this->ticket->getCreatedAt()),
            'resolved_at'   => $this->formatDateForRender($this->ticket->getResolvedAt()),
        ];
    }
}
