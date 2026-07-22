<?php declare(strict_types=1);

namespace Core\Domains\Infra\Tokens;

interface TokenRepositoryInterface
{
    public function save(array $data, ?string $id = null): string;

    public function find(string $token): null|array;

    public function drop(string $id): void;
}
