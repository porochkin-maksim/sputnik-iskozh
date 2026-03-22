<?php declare(strict_types=1);

namespace App\Imports\Payments;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class PaymentsImport implements WithMultipleSheets, WithCalculatedFormulas
{
    private int $colAccruedIndex;
    private int $colPaidIndex;
    private int $colDebtIndex;
    private int $sheetsCount;

    private array $sheetsData = [];

    public function __construct(
        string $colAccrued,
        string $colPaid,
        string $colDebt,
        int    $sheetsCount = 3, // по умолчанию 3 листа, можно передать извне
    )
    {
        $this->colAccruedIndex = $this->columnToIndex($colAccrued);
        $this->colPaidIndex    = $this->columnToIndex($colPaid);
        $this->colDebtIndex    = $this->columnToIndex($colDebt);
        $this->sheetsCount     = $sheetsCount;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        for ($i = 0; $i < $this->sheetsCount; $i++) {
            $sheets[$i] = new Sheet(
                $this,
                $i,
                $this->colAccruedIndex,
                $this->colPaidIndex,
                $this->colDebtIndex,
            );
        }

        return $sheets;
    }

    /**
     * Сохранить данные для конкретного листа
     */
    public function setSheetData(int $sheetIndex, array $data): void
    {
        $this->sheetsData[$sheetIndex] = $data;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getSheetsData(): array
    {
        return $this->sheetsData;
    }

    /**
     * Преобразует название колонки в индекс массива (0-based)
     * Поддерживает буквы (A-Z, AA-ZZ) и числа (1-999)
     */
    private function columnToIndex(string $column): int
    {
        $column = trim($column);

        // Если это число
        if (is_numeric($column)) {
            return (int) $column - 1;
        }

        // Если это буквы (A, B, C, ..., AA, AB и т.д.)
        $column = strtoupper($column);
        $length = strlen($column);
        $index  = 0;

        for ($i = 0; $i < $length; $i++) {
            $char  = $column[$i];
            $index = $index * 26 + (ord($char) - ord('A') + 1);
        }

        return $index - 1; // переводим в 0-базовый индекс
    }
}