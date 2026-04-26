<?php declare(strict_types=1);

namespace Core\Repositories\Models;

readonly class SortOrder
{
    public function __construct(
        private string $field,
        private string $value,
    )
    {
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
