<?php declare(strict_types=1);

namespace Core\Contracts;

interface StringServiceInterface
{
    public function random(int $length): string;

    public function replace(string $search, string $replace, string $subject): string;

    public function normalizePath(string $path): string;

    public function lower(string $value): string;

    public function remove(string $search, string $subject): string;

    public function slug(string $value): string;

    public function uuid(): string;

    public function uuidHex(): string;
}
