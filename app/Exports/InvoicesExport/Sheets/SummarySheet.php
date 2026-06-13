<?php declare(strict_types=1);

namespace App\Exports\InvoicesExport\Sheets;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SummarySheet extends BaseExportSheet
{
    /** @var array<string, array<string, array{cost:float, paid:float, count:int}>> */
    private array $groups = [];

    private int $row = 1;

    protected function buildSheet(Worksheet $sheet): void
    {
        $this->groupInvoices();
        $this->buildHeader($sheet);
        $this->buildData($sheet);
        $this->applyStyles($sheet);
    }

    private function groupInvoices(): void
    {
        foreach ($this->invoices as $invoice) {
            $periodName = $invoice->getPeriod()?->getName() ?? 'Без периода';
            $typeName   = $invoice->getType()?->name() ?? 'Без типа';
            $cost       = $invoice->getCost() ?? 0;
            $paid       = $invoice->getPaid() ?? 0;

            if ( ! isset($this->groups[$periodName])) {
                $this->groups[$periodName] = [];
            }
            if ( ! isset($this->groups[$periodName][$typeName])) {
                $this->groups[$periodName][$typeName] = ['cost' => 0, 'paid' => 0, 'count' => 0];
            }

            $this->groups[$periodName][$typeName]['cost']  += $cost;
            $this->groups[$periodName][$typeName]['paid']  += $paid;
            $this->groups[$periodName][$typeName]['count'] += 1;
        }
    }

    private function buildHeader(Worksheet $sheet): void
    {
        $sheet->fromArray([['Период', 'Тип счёта', 'Кол-во', 'Начислено', 'Оплачено', 'Долг', 'Переплата']], null, 'A1');
    }

    private function buildData(Worksheet $sheet): void
    {
        $grandTotal = ['cost' => 0, 'paid' => 0, 'count' => 0];

        ksort($this->groups);

        foreach ($this->groups as $periodName => $types) {
            $periodTotal = ['cost' => 0, 'paid' => 0, 'count' => 0];
            $firstRow    = $this->row + 1;

            ksort($types);

            foreach ($types as $typeName => $data) {
                $debt   = max($data['cost'] - $data['paid'], 0);
                $overpay = max($data['paid'] - $data['cost'], 0);

                $this->row++;
                $sheet->setCellValue([2, $this->row], $typeName);
                $sheet->setCellValue([3, $this->row], $data['count']);
                $sheet->setCellValue([4, $this->row], $data['cost']);
                $sheet->setCellValue([5, $this->row], $data['paid']);
                $sheet->setCellValue([6, $this->row], $debt);
                $sheet->setCellValue([7, $this->row], $overpay);

                $periodTotal['cost']  += $data['cost'];
                $periodTotal['paid']  += $data['paid'];
                $periodTotal['count'] += $data['count'];
            }

            $grandTotal['cost']  += $periodTotal['cost'];
            $grandTotal['paid']  += $periodTotal['paid'];
            $grandTotal['count'] += $periodTotal['count'];

            // Объединяем ячейку периода
            if ($firstRow !== $this->row) {
                $sheet->mergeCells("A{$firstRow}:A{$this->row}");
            }
            $sheet->setCellValue([1, $firstRow], $periodName);

            // Итого по периоду
            $this->row++;
            $debt   = max($periodTotal['cost'] - $periodTotal['paid'], 0);
            $overpay = max($periodTotal['paid'] - $periodTotal['cost'], 0);

            $sheet->setCellValue([1, $this->row], '');
            $sheet->setCellValue([2, $this->row], 'Итого по периоду');
            $sheet->setCellValue([3, $this->row], $periodTotal['count']);
            $sheet->setCellValue([4, $this->row], $periodTotal['cost']);
            $sheet->setCellValue([5, $this->row], $periodTotal['paid']);
            $sheet->setCellValue([6, $this->row], $debt);
            $sheet->setCellValue([7, $this->row], $overpay);

            $this->stylePeriodTotal($sheet);

            $this->row++;
        }

        // Общий итог
        $grandDebt    = max($grandTotal['cost'] - $grandTotal['paid'], 0);
        $grandOverpay = max($grandTotal['paid'] - $grandTotal['cost'], 0);

        $sheet->setCellValue([1, $this->row], '');
        $sheet->setCellValue([2, $this->row], 'ОБЩИЙ ИТОГ');
        $sheet->setCellValue([3, $this->row], $grandTotal['count']);
        $sheet->setCellValue([4, $this->row], $grandTotal['cost']);
        $sheet->setCellValue([5, $this->row], $grandTotal['paid']);
        $sheet->setCellValue([6, $this->row], $grandDebt);
        $sheet->setCellValue([7, $this->row], $grandOverpay);

        $this->styleGrandTotal($sheet);
    }

    private function stylePeriodTotal(Worksheet $sheet): void
    {
        $range = 'A' . $this->row . ':G' . $this->row;
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9E2F3'],
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);
    }

    private function styleGrandTotal(Worksheet $sheet): void
    {
        $range = 'A' . $this->row . ':G' . $this->row;
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);
        $sheet->getStyle($range)->getFont()->getColor()->setRGB('FFFFFF');
    }

    private function applyStyles(Worksheet $sheet): void
    {
        $lastRow = $sheet->getHighestRow();

        $headerRange = 'A1:G1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $this->setColumnWidths($sheet, [
            1 => 22, 2 => 18, 3 => 10, 4 => 14, 5 => 14, 6 => 14, 7 => 14,
        ]);

        if ($lastRow > 1) {
            $sheet->getStyle("D2:G{$lastRow}")->getNumberFormat()->setFormatCode('#,##0.00');
        }

        $sheet->freezePane('A2');
    }

    public function title(): string
    {
        return 'Сводка';
    }
}
