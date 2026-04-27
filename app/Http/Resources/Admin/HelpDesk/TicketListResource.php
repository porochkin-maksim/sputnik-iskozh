<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\HelpDesk;

use App\Http\Resources\AbstractResource;
use Core\Domains\HelpDesk\Collection\TicketCollection;

readonly class TicketListResource extends AbstractResource
{
    public function __construct(
        private TicketCollection $categoryCollection,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $result = [];

        foreach ($this->categoryCollection as $item) {
            $result[] = new TicketResource($item);
        }

        return $result;
    }
}
