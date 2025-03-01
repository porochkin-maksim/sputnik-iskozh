<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Services;

use App\Http\Resources\AbstractResource;
use Core\Domains\Billing\Service\Collections\ServiceCollection;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Resources\RouteNames;

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
            'historyUrl' => route(RouteNames::HISTORY_CHANGES, [
                'type' => HistoryType::SERVICE,
            ]),
        ];

        foreach ($this->serviceCollection as $service) {
            $result[] = new ServiceResource($service);
        }

        return $result;
    }
}
