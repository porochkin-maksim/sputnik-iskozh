<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\HelpDesk;

use App\Http\Resources\AbstractResource;
use Core\Domains\HelpDesk\Collection\TicketServiceCollection;

readonly class ServiceListResource extends AbstractResource
{
    public function __construct(
        private TicketServiceCollection $serviceCollection,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $result = [];

        foreach ($this->serviceCollection as $item) {
            $result[] = new ServiceResource($item);
        }

        return $result;
    }
}
