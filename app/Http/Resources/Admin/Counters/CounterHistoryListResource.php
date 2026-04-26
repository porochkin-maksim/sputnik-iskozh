<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Counters;

use App\Http\Resources\AbstractResource;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\CounterHistory\CounterHistoryCollection;
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
                'view' => $access->can(PermissionEnum::COUNTERS_VIEW),
                'edit' => $access->can(PermissionEnum::COUNTERS_EDIT),
                'drop' => $access->can(PermissionEnum::COUNTERS_DROP),
            ],
        ];

        if ($this->counterHistoryCollection->isEmpty()) {
            return $result;
        }

        foreach ($this->counterHistoryCollection as $counterHistory) {
            $result['histories'][] = new CounterHistoryResource($counterHistory);
        }

        return $result;
    }
}
