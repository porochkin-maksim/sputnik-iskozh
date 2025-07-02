<?php declare(strict_types=1);

namespace Core\Helpers\Phone;

class PhoneHelper
{
    /**
     * Нормализует номер телефона из строки в формат 8(900)000-00-00
     */
    public static function normalizePhone(?string $string): ?string
    {
        if (null === $string) {
            return null;
        }

        // Удаляем все нецифровые символы
        $digits = preg_replace('/[^0-9]/', '', $string);

        // Проверяем, что строка содержит 11 цифр и начинается с 8 или 7
        if (strlen($digits) !== 11 || ! in_array($digits[0], ['7', '8'])) {
            return null;
        }

        // Если номер начинается с 7, заменяем на 8
        if ($digits[0] === '7') {
            $digits = '8' . substr($digits, 1);
        }

        // Форматируем номер в нужный вид
        return sprintf(
            '%s(%s%s%s)%s%s%s-%s%s-%s%s',
            $digits[0],  // 8
            $digits[1],  // 9
            $digits[2],  // 0
            $digits[3],  // 0
            $digits[4],  // 0
            $digits[5],  // 0
            $digits[6],  // 0
            $digits[7],  // 0
            $digits[8],  // 0
            $digits[9],  // 0
            $digits[10],  // 0
        );
    }

    /**
     * Нормализует номер телефона в международном формате в формат +7(900)000-00-00
     */
    public static function normalizeInternationalPhone(?string $string): ?string
    {
        if (null === $string) {
            return null;
        }

        // Удаляем все нецифровые символы
        $digits = preg_replace('/[^0-9]/', '', $string);

        // Проверяем, что строка содержит 11 цифр и начинается с 7
        if (strlen($digits) !== 11 || $digits[0] !== '7') {
            return null;
        }

        // Форматируем номер в нужный вид
        return sprintf(
            '+%s(%s%s%s)%s%s%s-%s%s-%s%s',
            $digits[0],  // 7
            $digits[1],  // 9
            $digits[2],  // 0
            $digits[3],  // 0
            $digits[4],  // 0
            $digits[5],  // 0
            $digits[6],  // 0
            $digits[7],  // 0
            $digits[8],  // 0
            $digits[9],  // 0
            $digits[10],  // 0
        );
    }

    /**
     * Извлекает первый номер телефона из строки и нормализует его
     */
    public static function extractAndNormalizePhone(mixed $string): ?string
    {
        if (!$string) {
            return null;
        }

        try {
            $string = (string) $string;
        }
        catch (\Throwable) {
            return null;
        }
        // Ищем первый номер телефона в формате 8-XXX-XXX-XX-XX или +7XXXXXXXXXX
        preg_match('/(?:8|(?:\+7))[-\s]?\d{3}[-\s]?\d{3}[-\s]?\d{2}[-\s]?\d{2}/', $string, $matches);

        if (empty($matches)) {
            return null;
        }

        $phone = $matches[0];

        // Если номер начинается с +7, используем международный формат
        if (str_starts_with($phone, '+7')) {
            return self::normalizeInternationalPhone($phone);
        }

        return self::normalizePhone($phone);
    }
} 