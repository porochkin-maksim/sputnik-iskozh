<?php declare(strict_types=1);

namespace App\Helpers;

use Core\Domains\Shared\Contracts\StringGeneratorInterface;
use Illuminate\Support\Str;

class StringGenerator implements StringGeneratorInterface
{
    public function random(int $length): string
    {
        return Str::random($length);
    }

    public function replace(string $search, string $replace, string $subject): string
    {
        return Str::replace($search, $replace, $subject);
    }

    public function normalizePath(string $path): string
    {
        return Str::replace('//', '/', $path);
    }
}
