<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\HelpDesk;

use App\Http\Resources\AbstractResource;
use Core\Domains\HelpDesk\Models\TicketServiceDTO;

readonly class ServiceResource extends AbstractResource
{
    public function __construct(
        private TicketServiceDTO $serviceDTO,
    )
    {
    }


    public function jsonSerialize(): array
    {
        return [
            'id'          => $this->serviceDTO->getId(),
            'category_id' => $this->serviceDTO->getCategoryId(),
            'name'        => $this->serviceDTO->getName(),
            'code'        => $this->serviceDTO->getCode(),
            'sort_order'  => $this->serviceDTO->getSortOrder(),
            'is_active'   => $this->serviceDTO->getIsActive(),
            'created_at'  => $this->serviceDTO->getCreatedAt()?->toISOString(),
            'updated_at'  => $this->serviceDTO->getUpdatedAt()?->toISOString(),
            'category'    => $this->serviceDTO->getCategory() ? new CategoryResource($this->serviceDTO->getCategory()) : null,
        ];
    }
}
