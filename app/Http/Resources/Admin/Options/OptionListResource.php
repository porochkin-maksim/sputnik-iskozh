<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Options;

use App\Http\Resources\AbstractResource;
use Core\Domains\Option\OptionCollection;

readonly class OptionListResource extends AbstractResource
{
    public function __construct(
        private OptionCollection $optionCollection,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $result = [];

        foreach ($this->optionCollection as $option) {
            $result[] = new OptionResource($option);
        }

        return $result;
    }
}
