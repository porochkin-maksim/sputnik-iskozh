<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Options;

use App\Http\Resources\AbstractResource;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Domains\Option\Collections\OptionCollection;
use Core\Responses\ResponsesEnum;
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
            'historyUrl' => HistoryChangesLocator::route(
                type: HistoryType::INVOICE,
            ),
            'actions'    => [
                ResponsesEnum::VIEW => $access->can(PermissionEnum::OPTIONS_VIEW),
                ResponsesEnum::EDIT => $access->can(PermissionEnum::OPTIONS_EDIT),
            ],
        ];

        foreach ($this->optionCollection as $option) {
            $result['options'][] = new OptionResource($option);
        }

        return $result;
    }
}