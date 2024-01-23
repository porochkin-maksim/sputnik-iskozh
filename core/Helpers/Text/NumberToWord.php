<?php

namespace Core\Helpers\Text;

/**
 * перевод числа в словесную форму
 */
class NumberToWord
{
    const MAX_NUMBER = 999999999999; //12 знаков

    const GENDER_NEUTER    = 0;
    const GENDER_MASCULINE = 1;
    const GENDER_FEMININE  = 2;

    const GENDERS = [
        self::GENDER_NEUTER,
        self::GENDER_FEMININE,
        self::GENDER_MASCULINE
    ];

    const DECIMAL_BIT_100        = 100;
    const DECIMAL_BIT_1000       = 1000;
    const DECIMAL_BIT_1000000    = 1000000;
    const DECIMAL_BIT_1000000000 = 1000000000;

    const DECIMAL_BITS = [
        0 => self::DECIMAL_BIT_100,
        1 => self::DECIMAL_BIT_1000,
        2 => self::DECIMAL_BIT_1000000,
        3 => self::DECIMAL_BIT_1000000000,
    ];

    const NUM_WORDS = [
        self::GENDER_FEMININE  => [
            0   => 'ноль',
            1   => 'одна',
            2   => 'две',
            3   => 'три',
            4   => 'четыре',
            5   => 'пять',
            6   => 'шесть',
            7   => 'семь',
            8   => 'восемь',
            9   => 'девять',
            10  => 'десять',
            11  => 'одиннадцать',
            12  => 'двенадцать',
            13  => 'тринадцать',
            14  => 'четырнадцать',
            15  => 'пятнадцать',
            16  => 'шестнадцать',
            17  => 'семнадцать',
            18  => 'восемнадцать',
            19  => 'девятнадцать',
            20  => 'двадцать',
            30  => 'тридцать',
            40  => 'сорок',
            50  => 'пятьдесят',
            60  => 'шестьдесят',
            70  => 'семьдесят',
            80  => 'восемьдесят',
            90  => 'девяносто',
            100 => 'сто',
            200 => 'двести',
            300 => 'триста',
            400 => 'четыреста',
            500 => 'пятьсот',
            600 => 'шестьсот',
            700 => 'семьсот',
            800 => 'восемьсот',
            900 => 'девятьсот'
        ],
        self::GENDER_MASCULINE => [
            0   => 'ноль',
            1   => 'один',
            2   => 'два',
            3   => 'три',
            4   => 'четыре',
            5   => 'пять',
            6   => 'шесть',
            7   => 'семь',
            8   => 'восемь',
            9   => 'девять',
            10  => 'десять',
            11  => 'одиннадцать',
            12  => 'двенадцать',
            13  => 'тринадцать',
            14  => 'четырнадцать',
            15  => 'пятнадцать',
            16  => 'шестнадцать',
            17  => 'семнадцать',
            18  => 'восемнадцать',
            19  => 'девятнадцать',
            20  => 'двадцать',
            30  => 'тридцать',
            40  => 'сорок',
            50  => 'пятьдесят',
            60  => 'шестьдесят',
            70  => 'семьдесят',
            80  => 'восемьдесят',
            90  => 'девяносто',
            100 => 'сто',
            200 => 'двести',
            300 => 'триста',
            400 => 'четыреста',
            500 => 'пятьсот',
            600 => 'шестьсот',
            700 => 'семьсот',
            800 => 'восемьсот',
            900 => 'девятьсот'
        ],
        self::GENDER_NEUTER    => [
            0   => 'ноль',
            1   => 'одно',
            2   => 'два',
            3   => 'три',
            4   => 'четыре',
            5   => 'пять',
            6   => 'шесть',
            7   => 'семь',
            8   => 'восемь',
            9   => 'девять',
            10  => 'десять',
            11  => 'одиннадцать',
            12  => 'двенадцать',
            13  => 'тринадцать',
            14  => 'четырнадцать',
            15  => 'пятнадцать',
            16  => 'шестнадцать',
            17  => 'семнадцать',
            18  => 'восемнадцать',
            19  => 'девятнадцать',
            20  => 'двадцать',
            30  => 'тридцать',
            40  => 'сорок',
            50  => 'пятьдесят',
            60  => 'шестьдесят',
            70  => 'семьдесят',
            80  => 'восемьдесят',
            90  => 'девяносто',
            100 => 'сто',
            200 => 'двести',
            300 => 'триста',
            400 => 'четыреста',
            500 => 'пятьсот',
            600 => 'шестьсот',
            700 => 'семьсот',
            800 => 'восемьсот',
            900 => 'девятьсот'
        ]
    ];

    const NUMS_DECIMALS = [
        self::DECIMAL_BIT_1000       => ['тысяча','тысячи','тысяч'],
        self::DECIMAL_BIT_1000000    => ['миллион','миллиона','миллионов'],
        self::DECIMAL_BIT_1000000000 => ['миллиард','миллиарда','миллиардов']
    ];

    /**
     * переводит число в строку.
     * максимально допустимое значение числа self::MAX_NUMBER(для отрицательного то же самое ограничение)
     * род вводится для объекта относительно которого мы генерируем число
     */
    public static function convert(
        int $number,
        int $gender // self::GENDER_*
    ) : string
    {
        $resultArray  = [];

        $isNegative = $number < 0;

        $number = abs($number);

        if (!in_array($gender, self::GENDERS)) {
            throw new \Exception('Wrong param');
        }

        if ($number > self::MAX_NUMBER) {
            throw new \Exception('Max number is ' . self::MAX_NUMBER);
        }



        if ($number === 0) {
            return self::NUM_WORDS[$gender][$number];
        }

        $triples = self::_dissect($number, 3);

        for ($i = 0; $i < count($triples); $i++) {

            $resultTriple = self::_createTriple($triples[$i], self::DECIMAL_BITS[$i], $gender);

            //при каждой новой тройке я формирую
            foreach ($resultTriple as $tripleItem) {
                array_unshift($resultArray, $tripleItem);
            }
        }

        //минус именно в начале массива
        if ($isNegative) {
            array_unshift($resultArray, 'минус');
        }

        return join(' ', $resultArray);
    }

    /**
     * делит число  на группы любого размера, как укажете
     * число 111222333444 с разбиением 3 вернет результат [444, 333, 222, 111]
     */
    protected static function _dissect(int $number, int $dissectSize) : array
    {
        if ($dissectSize <= 0) {
            return [];
        }

        $triples = [];

        $dissectValue = pow(10, $dissectSize);

        while ($number > 0) {

            $remainder = $number % $dissectValue;

            if (($remainder) == 0) {
                $number /= $dissectValue;
                array_push($triples, $remainder);
            } else {
                $number = ($number - $remainder) / $dissectValue;
                array_push($triples, $remainder);
            }
        }

        return $triples;
    }

    /**
     * генерирует словесное представление для одной тройки
     */
    protected static function _createTriple(
        int    $number,
        int    $decimalBit, // one of self::DECIMAL_BITS
        string $gender      // one of self::GENDERS
    ) : array
    {
        if ($number == 0) {
            return [];
        }

        $resultTriple       = self::_dissect($number, 1);
        $resultArray        = [];
        $resultTripleLength = count($resultTriple);

        if ($decimalBit == self::DECIMAL_BIT_100) {

            $decimalBitLocal = 1;
            $i               = 0;

            //если остаток % 100 попадает в 10-19, напр 113, то нужно обработать его по особому алгоритму, т.к. все
            //названия чисел там уникальны
            if ($number % 100 >= 10 && $number % 100 <= 19) {
                array_push($resultArray, self::NUM_WORDS[$gender][$number % 100]);

                //сразу переходим к сотням
                $i = 2;
                $decimalBitLocal *= 100;

                if ($number < 100) {
                    return $resultArray;
                }
            }

            //попадаем сюда если есть сотни
            //так как я работаю с обратным массивом то у меня
            //сначала идут меньшие разряды [0]единицы [1]десятки [2]сотни
            for (; $i < $resultTripleLength; $i++) {
                if ($resultTriple[$i] != 0) {
                    array_push($resultArray, self::NUM_WORDS[$gender][$resultTriple[$i] * $decimalBitLocal]);
                }

                //повышаем разряд
                $decimalBitLocal *= 10;
            }
        } else {

            //[0]тысяча
            $resultArray[] = NumEnding::numEnding($number, self::NUMS_DECIMALS[$decimalBit]);

            //[0]тысяча [1]одна
            $secondaryGender = ($decimalBit === self::DECIMAL_BIT_1000) ? self::GENDER_FEMININE : self::GENDER_MASCULINE;

            $secondaryTriple = self::_createTriple($number, self::DECIMAL_BIT_100, $secondaryGender);
            foreach ($secondaryTriple as $item) {
                array_push($resultArray, $item);
            }
        }

        return $resultArray;
    }
}
