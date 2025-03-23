<?php declare(strict_types=1);

namespace Core\Enums;

/**
 * @method static tryFrom(int|string|null $value): ?static
 */
trait EnumCommonTrait
{
    public static function names(): array
    {
        $result = [];
        foreach (self::cases() as $key => $value) {
            $result[$value->value] = $value->name();
        }

        return $result;
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

    public function toArray(): array
    {
        return [
            'key'   => $this->value,
            'value' => $this->name(),
        ];
    }

    public static function getRandomValue(): string|array|int
    {
        return array_rand(self::cases());
    }
}
