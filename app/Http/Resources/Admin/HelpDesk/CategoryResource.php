<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\HelpDesk;

use Core\Domains\HelpDesk\Models\TicketCategoryDTO;
use App\Http\Resources\AbstractResource;

readonly class CategoryResource extends AbstractResource
{
    public function __construct(
        private TicketCategoryDTO $categoryDTO,
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'id'         => $this->categoryDTO->getId(),
            'type'       => $this->categoryDTO->getType()?->value,
            'type_name'  => $this->categoryDTO->getType()?->name(),
            'name'       => $this->categoryDTO->getName(),
            'code'       => $this->categoryDTO->getCode(),
            'sort_order' => $this->categoryDTO->getSortOrder(),
            'is_active'  => $this->categoryDTO->getIsActive(),
            'created_at' => $this->categoryDTO->getCreatedAt()?->toISOString(),
            'updated_at' => $this->categoryDTO->getUpdatedAt()?->toISOString(),
        ];
    }
}
