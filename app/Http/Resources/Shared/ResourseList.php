<?php declare(strict_types=1);

namespace App\Http\Resources\Shared;

use App\Http\Resources\AbstractResource;
use Core\Shared\Collections\Collection;
use Exception;

readonly class ResourseList extends AbstractResource
{
    public function __construct(
        private Collection $collection,
        private string     $resourceClass,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $result = [];

        try {
            foreach ($this->collection as $item) {
                $result[] = new $this->resourceClass($item);
            }
        }
        catch (Exception) {
        }

        return $result;
    }
}