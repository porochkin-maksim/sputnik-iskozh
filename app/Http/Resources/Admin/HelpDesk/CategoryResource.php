<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\HelpDesk;

use App\Http\Resources\AbstractResource;
use Core\Domains\HelpDesk\Models\TicketCategoryEntity;

readonly class CategoryResource extends AbstractResource
{
    public function __construct(
        private TicketCategoryEntity $category,
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'id'         => $this->category->getId(),
            'type'       => $this->category->getType()?->value,
            'type_name'  => $this->category->getType()?->name(),
            'name'       => $this->category->getName(),
            'code'       => $this->category->getCode(),
            'sort_order' => $this->category->getSortOrder(),
            'is_active'  => $this->category->getIsActive(),
            'created_at' => $this->category->getCreatedAt()?->toISOString(),
            'updated_at' => $this->category->getUpdatedAt()?->toISOString(),
        ];
    }
}
