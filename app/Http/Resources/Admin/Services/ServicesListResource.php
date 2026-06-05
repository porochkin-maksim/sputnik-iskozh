<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Services;

use App\Http\Resources\AbstractResource;
use Core\Domains\Billing\Service\ServiceCollection;

readonly class ServicesListResource extends AbstractResource
{
    public function __construct(
        private ServiceCollection $serviceCollection,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $result = [];
        foreach ($this->serviceCollection as $service) {
            $result[] = new ServiceResource($service);
        }

        return $result;
    }
}
