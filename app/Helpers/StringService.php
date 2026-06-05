<?php declare(strict_types=1);

namespace App\Helpers;

use Core\Contracts\StringServiceInterface;
use Illuminate\Support\Str;

class StringService implements StringServiceInterface
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

    public function lower(string $value): string
    {
        return Str::lower($value);
    }

    public function remove(string $search, string $subject): string
    {
        return Str::remove($search, $subject);
    }

    public function slug(string $value): string
    {
        return Str::slug($value);
    }

    public function uuid(): string
    {
        return Str::uuid()->toString();
    }

    public function uuidHex(): string
    {
        return Str::uuid()->getHex()->toString();
    }
}
