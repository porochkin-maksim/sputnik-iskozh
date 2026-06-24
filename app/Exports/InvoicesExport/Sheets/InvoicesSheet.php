<?php declare(strict_types=1);

namespace App\Exports\InvoicesExport\Sheets;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoicesSheet extends BaseExportSheet
{
    protected function buildSheet(Worksheet $sheet): void
    {
        $headers = [
            '№', 'Название / Тип', 'Период', 'Участок',
            'Стоимость', 'Оплачено', 'Долг',
        ];
        $rows = [$headers];

        foreach ($this->invoices as $invoice) {
            $cost = $invoice->getCost() ?? 0;
            $paid = $invoice->getPaid() ?? 0;
            $delta = $cost - $paid;
            $displayCost = $cost;
            $displayDebt = $delta;
            $type = $invoice->getType();
            $name = $invoice->getName();
            $displayName = $name ? $type?->name() . ' / ' . $name : ($type?->name() ?? '');

            $rows[] = [
                $invoice->getId(),
                $displayName,
                $invoice->getPeriod()?->getName(),
                $invoice->getAccount()?->getNumber(),
                $displayCost,
                $paid,
                $displayDebt,
            ];
        }

        $sheet->fromArray($rows, null, 'A1');

        $this->applyStyles($sheet);
    }

    private function applyStyles(Worksheet $sheet): void
    {
        $lastColumn = 7;
        $lastRow    = $sheet->getHighestRow();

        $headerRange = 'A1:' . $this->colLetter($lastColumn) . '1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $dataRange = 'A2:' . $this->colLetter($lastColumn) . $lastRow;
        $sheet->getStyle($dataRange)->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        $this->setColumnWidths($sheet, [
            1 => 8, 2 => 28, 3 => 20, 4 => 15, 5 => 14, 6 => 14, 7 => 14,
        ]);

        if ($lastRow > 1) {
            $sheet->getStyle("E2:G{$lastRow}")->getNumberFormat()->setFormatCode('#,##0.00');
        }

        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:' . $this->colLetter($lastColumn) . $lastRow);
    }

    public function title(): string
    {
        return 'Счета';
    }
}
