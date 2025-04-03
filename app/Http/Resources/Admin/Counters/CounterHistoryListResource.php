<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Counters;

use App\Http\Resources\AbstractResource;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Counter\Collections\CounterHistoryCollection;
use Core\Responses\ResponsesEnum;
use lc;

readonly class CounterHistoryListResource extends AbstractResource
{
    public function __construct(
        private CounterHistoryCollection $counterHistoryCollection,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access = lc::roleDecorator();
        $result = [
            'histories' => [],
            'actions'   => [
                ResponsesEnum::VIEW => $access->can(PermissionEnum::COUNTERS_VIEW),
                ResponsesEnum::EDIT => $access->can(PermissionEnum::COUNTERS_EDIT),
                ResponsesEnum::DROP => $access->can(PermissionEnum::COUNTERS_DROP),
            ],
        ];

        if ($this->counterHistoryCollection->isEmpty()) {
            return $result;
        }

        foreach ($this->counterHistoryCollection as $counterHistory) {
            $result['histories'][] = new CounterHistoryResource($counterHistory, $this->counterHistoryCollection->getById($counterHistory->getPreviousId()));
        }

        return $result;
    }
}