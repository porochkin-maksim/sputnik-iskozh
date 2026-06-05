<?php declare(strict_types=1);

namespace App\Services\Db;

use Core\Contracts\DbServiceInterface;
use Illuminate\Database\DatabaseManager;

readonly class DbService implements DbServiceInterface
{
    public function __construct(
        private DatabaseManager $database,
    )
    {
    }

    public function transaction(callable $callback): mixed
    {
        return $this->database->transaction($callback);
    }

    public function beginTransaction(): void
    {
        $this->database->beginTransaction();
    }

    public function commit(): void
    {
        $this->database->commit();
    }

    public function rollBack(): void
    {
        $this->database->rollBack();
    }

    public function raw(string $value): mixed
    {
        return $this->database->raw($value);
    }

    public function select(string $query, array $bindings = []): array
    {
        return $this->database->select($query, $bindings);
    }
}
