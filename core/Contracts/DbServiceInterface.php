<?php declare(strict_types=1);

namespace Core\Contracts;

interface DbServiceInterface
{
    public function transaction(callable $callback): mixed;

    public function beginTransaction(): void;

    public function commit(): void;

    public function rollBack(): void;

    public function raw(string $query): mixed;

    public function select(string $query, array $bindings = []): array;
}
