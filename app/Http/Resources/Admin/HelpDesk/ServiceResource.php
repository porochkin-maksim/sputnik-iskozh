<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\HelpDesk;

use App\Http\Resources\AbstractResource;
use Core\Domains\HelpDesk\Models\TicketServiceEntity;

readonly class ServiceResource extends AbstractResource
{
    public function __construct(
        private TicketServiceEntity $service,
    )
    {
    }


    public function jsonSerialize(): array
    {
        return [
            'id'          => $this->service->getId(),
            'category_id' => $this->service->getCategoryId(),
            'name'        => $this->service->getName(),
            'code'        => $this->service->getCode(),
            'sort_order'  => $this->service->getSortOrder(),
            'is_active'   => $this->service->getIsActive(),
            'created_at'  => $this->service->getCreatedAt()?->toISOString(),
            'updated_at'  => $this->service->getUpdatedAt()?->toISOString(),
            'category'    => $this->service->getCategory() ? new CategoryResource($this->service->getCategory()) : null,
        ];
    }
}
