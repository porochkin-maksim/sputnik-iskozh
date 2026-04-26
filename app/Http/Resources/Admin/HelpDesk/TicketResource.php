<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\HelpDesk;

use App\Http\Resources\AbstractResource;
use App\Http\Resources\Admin\Accounts\AccountResource;
use App\Http\Resources\Admin\Users\UserResource;
use Core\Domains\HelpDesk\Models\TicketEntity;
use App\Resources\RouteNames;

readonly class TicketResource extends AbstractResource
{
    public function __construct(
        private TicketEntity $ticket,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $result = [
            'id'            => $this->ticket->getId(),
            'type'          => $this->ticket->getType()?->value,
            'type_name'     => $this->ticket->getType()?->name(),
            'type_icon'     => $this->ticket->getType()?->icon(),
            'type_color'    => $this->ticket->getType()?->color(),
            'category_id'   => $this->ticket->getCategoryId(),
            'category_name' => $this->ticket->getCategory()?->getName(),
            'service_id'    => $this->ticket->getServiceId(),
            'service_name'  => $this->ticket->getService()?->getName(),
            'priority'      => $this->ticket->getPriority()?->value,
            'priority_name' => $this->ticket->getPriority()?->name(),
            'status'        => $this->ticket->getStatus()?->value,
            'status_name'   => $this->ticket->getStatus()?->name(),
            'description'   => $this->ticket->getDescription(),
            'result'        => $this->ticket->getResult(),
            'contact_name'  => $this->ticket->getContactName(),
            'contact_phone' => $this->ticket->getContactPhone(),
            'contact_email' => $this->ticket->getContactEmail(),
            'user_id'       => $this->ticket->getUserId(),
            'user'          => $this->ticket->getUser() ? new UserResource($this->ticket->getUser()) : null,
            'account_id'    => $this->ticket->getAccountId(),
            'account'       => $this->ticket->getAccount() ? new AccountResource($this->ticket->getAccount()) : null,
            'resolved_at'   => $this->ticket->getResolvedAt()?->toISOString(),
            'created_at'    => $this->ticket->getCreatedAt()?->toISOString(),
            'updated_at'    => $this->ticket->getUpdatedAt()?->toISOString(),

            'viewUrl' => route(RouteNames::ADMIN_HELP_DESK . '.tickets.view', $this->ticket->getId()),
        ];

        if ($this->ticket->getFiles()) {
            $result['files'] = $this->ticket->getFiles();
        }

        if ($this->ticket->getResultFiles()) {
            $result['result_files'] = $this->ticket->getResultFiles();
        }

        return $result;
    }
}