<?php declare(strict_types=1);

namespace App\Exports\InvoicesExport\Sheets;

use Core\Domains\Billing\Invoice\InvoiceCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class BaseExportSheet implements WithEvents, WithTitle
{
    public function __construct(
        protected readonly InvoiceCollection|array $invoices,
    )
    {
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $this->buildSheet($sheet);
                $sheet->setTitle($this->title());
            },
        ];
    }

    abstract protected function buildSheet(Worksheet $sheet): void;

    abstract public function title(): string;

    protected function colLetter(int $index): string
    {
        return Coordinate::stringFromColumnIndex($index);
    }

    protected function setColumnWidths(Worksheet $sheet, array $widths): void
    {
        foreach ($widths as $colIndex => $width) {
            $sheet->getColumnDimension($this->colLetter($colIndex))->setWidth($width);
        }
    }
}
