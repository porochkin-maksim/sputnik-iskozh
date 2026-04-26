<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Options;

use App\Http\Resources\AbstractResource;
use App\Support\HistoryChangesRoute;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\HistoryChanges\HistoryType;
use Core\Domains\Option\OptionCollection;
use lc;

readonly class OptionListResource extends AbstractResource
{
    public function __construct(
        private OptionCollection $optionCollection,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access = lc::roleDecorator();
        $result = [
            'options'    => [],
            'historyUrl' => HistoryChangesRoute::make(
                type: HistoryType::OPTION,
            ),
            'actions'    => [
                'view' => $access->can(PermissionEnum::OPTIONS_VIEW),
                'edit' => $access->can(PermissionEnum::OPTIONS_EDIT),
            ],
        ];

        foreach ($this->optionCollection as $option) {
            $result['options'][] = new OptionResource($option);
        }

        return $result;
    }
}
