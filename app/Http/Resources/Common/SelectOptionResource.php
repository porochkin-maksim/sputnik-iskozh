<?php declare(strict_types=1);

namespace App\Http\Resources\Common;

use App\Http\Resources\AbstractResource;

readonly class SelectOptionResource extends AbstractResource
{
    public function __construct(
        private int|string $key,
        private int|string $value,
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'key'   => (string) $this->key,
            'value' => (string) $this->value,
        ];
    }
}
