<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Claims;

use App\Http\Resources\AbstractResource;
use App\Http\Resources\Admin\Services\ServiceResource;
use Core\Domains\Billing\Service\Collections\ServiceCollection;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;

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
            'historyUrl' => HistoryChangesLocator::route(
                type: HistoryType::SERVICE,
            ),
        ];

        foreach ($this->serviceCollection as $service) {
            $result[] = new ServiceResource($service);
        }

        return $result;
    }
}
