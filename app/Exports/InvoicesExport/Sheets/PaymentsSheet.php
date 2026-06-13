<?php declare(strict_types=1);

namespace App\Exports\InvoicesExport\Sheets;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentsSheet extends BaseExportSheet
{
    protected function buildSheet(Worksheet $sheet): void
    {
        $headers = [
            'Участок', 'Период', 'Тип счёта', '№ счёта',
            'Платёж', 'Сумма', 'Дата платежа', 'Примечание',
        ];
        $rows = [$headers];

        foreach ($this->invoices as $invoice) {
            $payments = $invoice->getPayments();
            if (!$payments) {
                continue;
            }

            $account = $invoice->getAccount();
            $period  = $invoice->getPeriod();
            $type    = $invoice->getType();

            foreach ($payments as $payment) {
                $paidAt = $payment->getPaidAt();

                $rows[] = [
                    $account?->getNumber(),
                    $period?->getName(),
                    $type?->name(),
                    $invoice->getId(),
                    $payment->getName() ?: 'Платёж #' . $payment->getId(),
                    $payment->getCost(),
                    $paidAt?->format('d.m.Y H:i'),
                    $payment->getComment(),
                ];
            }
        }

        $sheet->fromArray($rows, null, 'A1');

        $this->applyStyles($sheet);
    }

    private function applyStyles(Worksheet $sheet): void
    {
        $lastColumn = 8;
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
            1 => 15, 2 => 20, 3 => 15, 4 => 10, 5 => 25, 6 => 12, 7 => 18, 8 => 30,
        ]);

        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:' . $this->colLetter($lastColumn) . $lastRow);
    }

    public function title(): string
    {
        return 'Платежи';
    }
}
