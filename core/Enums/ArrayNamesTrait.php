<?php declare(strict_types=1);

namespace Core\Enums;

trait ArrayNamesTrait
{
    public static function array(): array
    {
        $result = [];

        foreach (self::cases() as $key => $value) {
            $result[$value->value] = static::from($value->value)->name();
        }

        return $result;
    }

    public static function json(): array
    {
        $result = [];

        foreach (self::array() as $key => $value) {
            $result[] = ['key' => $key, 'value' => $value];
        }

        return $result;
    }
}
