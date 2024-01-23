<?php

namespace Core\Helpers\Text;

/**
 * возвращает окончание для слова в зависимости от впереди-стоящего числа
 * К примеру:
 * 1 комментариЙ,
 * 4 комментариЯ,
 * 5 комментариЕВ
 */
class NumEnding
{
    /**
     *  Функция возвращает окончание для множественного числа слова на основании числа и массива окончаний
     *
     * @param int   $number  Число на основе которого нужно сформировать окончание
     * @param array $endings Массив слов или окончаний для чисел (1, 4, 5), например ['яблоко', 'яблока', 'яблок']
     *
     * @return string
     */
    public static function numEnding(
        int   $number,
        array $endings,
    ): string
    {
        $endings = array_values($endings);

        $number = abs($number);
        $number = $number % 100;

        if ($number >= 11 && $number <= 19) {
            return $endings[2];
        } else {
            return match ($number % 10) {
                1 => $endings[0],
                2, 3, 4 => $endings[1],
                default => $endings[2],
            };
        }
    }
}
