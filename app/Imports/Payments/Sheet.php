<?php declare(strict_types=1);

namespace App\Imports\Payments;

use Core\Domains\Enums\Regexp;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class Sheet implements ToArray, WithCalculatedFormulas
{
    public const string ACCOUNT_NUMBER = 'account_number';
    public const string COST           = 'cost';
    public const string PAID           = 'paid';
    public const string DEBT           = 'debt';

    private int $accountNumberColumnIndex = 1; // по умолчанию колонка B (индекс 1)

    public function __construct(
        private PaymentsImport $parent,
        private readonly int   $sheetIndex,
        private readonly int   $colAccruedIndex,
        private readonly int   $colPaidIndex,
        private readonly int   $colDebtIndex,
        ?int                   $accountNumberColumnIndex = null,
    )
    {
        if ($accountNumberColumnIndex !== null) {
            $this->accountNumberColumnIndex = $accountNumberColumnIndex;
        }
    }

    protected function sheetIndex(): int
    {
        return $this->sheetIndex;
    }

    public function array(array $array): void
    {
        $processed = [];

        foreach ($array as $row) {
            // Извлекаем номер участка (по умолчанию из колонки B)
            $accountNumber = $row[$this->accountNumberColumnIndex] ?? null;

            // Извлекаем суммы из указанных колонок
            $accrued = $row[$this->colAccruedIndex] ?? null;
            $paid    = $row[$this->colPaidIndex] ?? null;
            $delta   = $row[$this->colDebtIndex] ?? null;

            // Пропускаем строки без номера участка
            if ( ! $accountNumber) {
                continue;
            }

            // Очищаем номер участка от лишних символов
            $accountNumber = str_replace('-', '', (string) $accountNumber) . '/' . ($this->sheetIndex() + 1);

            if ( ! preg_match(Regexp::ACCOUNT_NAME, $accountNumber)) {
                continue;
            }

            // Преобразуем значения в числа
            $accrued = is_numeric($accrued) ? (float) $accrued : 0;
            $paid    = is_numeric($paid) ? (float) $paid : 0;
            $delta   = is_numeric($delta) ? (float) $delta : 0;

            $processed[] = [
                self::ACCOUNT_NUMBER => $accountNumber,
                self::COST           => $accrued,
                self::PAID           => $paid,
                self::DEBT           => $delta,
            ];
        }

        $this->parent->setSheetData($this->sheetIndex(), $processed);
    }
}
