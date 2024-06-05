<?php declare(strict_types=1);

namespace Core\Db\Searcher\Models;

readonly class Where
{
    public function __construct(
        private string $field,
        private string $operator,
        private mixed  $value,
    )
    {
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
