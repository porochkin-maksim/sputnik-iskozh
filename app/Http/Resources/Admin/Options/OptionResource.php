<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Options;

use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Option\Models\OptionDTO;
use Core\Resources\RouteNames;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OptionResource extends JsonResource
{
    private OptionDTO $option;

    public function __construct(OptionDTO $option)
    {
        parent::__construct($option);
        $this->option = $option;
    }

    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->option->getId(),
            'type'       => $this->option->getType()?->value,
            'name'       => $this->option->getType()?->name(),
            'data'       => $this->option->getData() ? new OptionDataResource($this->option->getData()) : null,
            'created_at' => $this->option->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updated_at' => $this->option->getUpdatedAt()?->format('Y-m-d H:i:s'),
            'historyUrl' => $this->option->getId() ? route(RouteNames::HISTORY_CHANGES, [
                'type'      => HistoryType::OPTION,
                'primaryId' => $this->option->getId(),
            ]) : null,
        ];
    }
}