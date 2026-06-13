<?php declare(strict_types=1);

namespace App\Exports\InvoicesExport\Sheets;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClaimsSheet extends BaseExportSheet
{
    protected function buildSheet(Worksheet $sheet): void
    {
        $headers = [
            'Участок', 'Период', 'Тип счёта', '№ счёта',
            'Услуга', 'Кол-во', 'Тариф', 'Стоимость', 'Оплачено', 'Долг',
        ];
        $rows = [$headers];

        foreach ($this->invoices as $invoice) {
            $claims = $invoice->getClaims();
            if (!$claims) {
                continue;
            }

            $account = $invoice->getAccount();
            $period  = $invoice->getPeriod();
            $type    = $invoice->getType();

            foreach ($claims as $claim) {
                $service = $claim->getService();
                $name    = $service?->getName() ?: $service?->getType()?->name();
                $delta   = $claim->getCost() !== null && $claim->getPaid() !== null
                    ? $claim->getCost() - $claim->getPaid()
                    : null;

                $rows[] = [
                    $account?->getNumber(),
                    $period?->getName(),
                    $type?->name(),
                    $invoice->getId(),
                    $name,
                    $claim->getQuantity(),
                    $claim->getTariff(),
                    $claim->getCost(),
                    $claim->getPaid(),
                    $delta,
                ];
            }
        }

        $sheet->fromArray($rows, null, 'A1');

        $this->applyStyles($sheet);
    }

    private function applyStyles(Worksheet $sheet): void
    {
        $lastColumn = 10;
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
            1 => 15, 2 => 20, 3 => 15, 4 => 10, 5 => 30,
            6 => 8, 7 => 12, 8 => 12, 9 => 12, 10 => 12,
        ]);

        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:' . $this->colLetter($lastColumn) . $lastRow);
    }

    public function title(): string
    {
        return 'Услуги';
    }
}
