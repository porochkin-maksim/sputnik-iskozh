<?php declare(strict_types=1);

namespace App\Exports\InvoicesExport\Sheets;

use Core\Domains\Billing\Invoice\Collections\InvoiceCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class BaseSheet implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithEvents
{
    public function __construct(
        protected InvoiceCollection|array $invoices,
        protected array $headers
    ) {}

    public function headings(): array
    {
        return array_values($this->headers);
    }

    public function collection(): Collection
    {
        $result = [];

        foreach ($this->invoices as $invoice) {
            $row = array_fill_keys(array_keys($this->headers), 0);

            $row['id']      = $invoice->getId();
            $row['period']  = $invoice->getPeriod()?->getName();
            $row['account'] = $invoice->getAccount()?->getNumber();
            $row['type']    = $invoice->getType()?->name();
            $row['cost']    = $invoice->getCost();
            $row['payed']   = $invoice->getPayed();
            $row['delta']   = $invoice->getCost() - $invoice->getPayed();

            foreach ($invoice->getClaims() as $claim) {
                $claimName = $claim->getService()?->getName() ?: $claim->getService()?->getType()?->name();
                $row[$claimName . 'cost']  = $claim->getCost();
                $row[$claimName . 'payed'] = $claim->getPayed();
            }

            $result[] = $row;
        }

        return collect($result);
    }

    public function styles(Worksheet $sheet): void
    {
        $lastColumn = $sheet->getHighestColumn();
        $lastRow = $sheet->getHighestRow();

        // Стили для заголовков
        $headerStyle = [
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => '4472C4',
                ],
            ],
        ];

        // Стили для данных
        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        // Применяем стили к заголовкам
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray($headerStyle);
        
        // Применяем стили к данным
        $sheet->getStyle('A2:' . $lastColumn . $lastRow)->applyFromArray($dataStyle);
        
        // Устанавливаем фильтр
        $sheet->setAutoFilter('A1:' . $lastColumn . $lastRow);
        
        // Автоматическая ширина столбцов
        foreach(range('A', $lastColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Замораживаем первую строку
        $sheet->freezePane('A2');
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setTitle($this->title());
            },
        ];
    }

    abstract public function title(): string;
} 