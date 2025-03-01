<?php declare(strict_types=1);

namespace App\Http\Resources\Common;

use App\Http\Resources\AbstractResource;

readonly class SelectResource extends AbstractResource
{
    public function __construct(
        private array $args
    )
    {
    }

    public function jsonSerialize(): array
    {
        $result = [];

        foreach ($this->args as $key => $value) {
            $result[] = new SelectOptionResource((string) $key, (string) $value);
        }

        return $result;
    }
}
