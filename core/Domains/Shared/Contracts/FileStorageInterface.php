<?php declare(strict_types=1);

namespace Core\Domains\Shared\Contracts;

interface FileStorageInterface
{
    public function put(string $path, string $content, bool $public = false): bool;

    public function get(string $path): ?string;

    public function delete(string $path): bool;

    public function exists(string $path): bool;

    public function copy(string $from, string $to): bool;

    public function getVisibility(string $path): string;

    public function setVisibility(string $path, string $visibility): bool;
}
