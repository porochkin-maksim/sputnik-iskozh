<?php declare(strict_types=1);

namespace Core\Enums;

trait EnumCommonTrait
{
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

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
