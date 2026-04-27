<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\HelpDesk;

use Core\Domains\HelpDesk\Collection\TicketCategoryCollection;
use App\Http\Resources\AbstractResource;

readonly class CategoryListResource extends AbstractResource
{
    public function __construct(
        private TicketCategoryCollection $categoryCollection,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $result = [];

        foreach ($this->categoryCollection as $item) {
            $result[] = new CategoryResource($item);
        }

        return $result;
    }
}
