<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Claims;

use App\Http\Resources\AbstractResource;
use App\Http\Resources\Admin\Services\ServiceResource;
use App\Support\HistoryChangesRoute;
use Core\Domains\Billing\Service\ServiceCollection;
use Core\Domains\HistoryChanges\HistoryType;

readonly class ServicesListResource extends AbstractResource
{
    public function __construct(
        private ServiceCollection $serviceCollection,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $result = [
            'historyUrl' => HistoryChangesRoute::make(
                type: HistoryType::SERVICE,
            ),
        ];

        foreach ($this->serviceCollection as $service) {
            $result[] = new ServiceResource($service);
        }

        return $result;
    }
}
