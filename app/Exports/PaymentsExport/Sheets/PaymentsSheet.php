<?php declare(strict_types=1);

namespace App\Exports\PaymentsExport\Sheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentsSheet implements FromCollection, WithHeadings, WithStyles, WithEvents, WithTitle
{
    public function __construct(
        private readonly array  $payments,
        private readonly array  $headers,
        private readonly string $title,
    )
    {
    }

    public function title(): string
    {
        return $this->title;
    }

    public function headings(): array
    {
        return array_values($this->headers);
    }

    public function collection(): Collection
    {
        $rows = [];

        foreach ($this->payments as $payment) {
            $rows[] = [
                'id'         => $payment['id'],
                'account'    => $payment['account_number'],
                'cost'       => $payment['cost'],
                'paid_at'    => $payment['paid_at'],
                'created_at' => $payment['created_at'],
                'verified'   => $payment['verified'] ? 'Да' : 'Нет',
                'moderated'  => $payment['moderated'] ? 'Да' : 'Нет',
                'comment'    => $payment['comment'],
            ];
        }

        return collect($rows);
    }

    public function styles(Worksheet $sheet): void
    {
        $lastColumn = $sheet->getHighestColumn();
        $lastRow    = $sheet->getHighestRow();

        $headerStyle = [
            'font'      => [
                'bold'  => true,
                'size'  => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders'   => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => '000000'],
                ],
            ],
            'fill'      => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
        ];

        $dataStyle = [
            'borders'   => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => '000000'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray($headerStyle);
        $sheet->getStyle('A2:' . $lastColumn . $lastRow)->applyFromArray($dataStyle);

        $stripedColor = 'F5F5F5';
        for ($r = 2; $r <= $lastRow; $r += 2) {
            $sheet->getStyle("A{$r}:{$lastColumn}{$r}")
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB($stripedColor);
        }

        $sheet->setAutoFilter('A1:' . $lastColumn . $lastRow);

        foreach ([1 => 8, 2 => 18, 3 => 14, 4 => 16, 5 => 16, 6 => 12, 7 => 14, 8 => 30] as $col => $width) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($col))->setWidth($width);
        }

        $sheet->freezePane('A2');
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->setTitle($this->title);
            },
        ];
    }
}
