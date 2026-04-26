<?php declare(strict_types=1);

namespace Core\Domains\Shared\Contracts;

interface StringGeneratorInterface
{
    public function random(int $length): string;

    public function replace(string $search, string $replace, string $subject): string;

    public function normalizePath(string $path): string;
}