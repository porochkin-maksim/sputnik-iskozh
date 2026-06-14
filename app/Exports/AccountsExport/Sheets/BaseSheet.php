<?php declare(strict_types=1);

namespace App\Exports\AccountsExport\Sheets;

use Core\Domains\Account\AccountCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class BaseSheet implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    public const string ID       = 'id';
    public const string NUMBER   = 'number';
    public const string SIZE     = 'size';
    public const string CADASTRE = 'cadastre';

    public function __construct(
        protected AccountCollection|array $accounts,
        protected array                   $headers,
    )
    {
    }

    public function headings(): array
    {
        return array_values($this->headers);
    }

    public function collection(): Collection
    {
        $rows = [];

        foreach ($this->accounts as $account) {
            $exData = $account->getExData();

            $rows[] = [
                self::ID       => $account->getId(),
                self::NUMBER   => $account->getNumber(),
                self::SIZE     => $account->getSize(),
                self::CADASTRE => $exData?->getCadastreNumber(),
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
                ->getStartColor()->setRGB($stripedColor)
            ;
        }

        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D2:D' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setAutoFilter('A1:' . $lastColumn . $lastRow);

        foreach ([1 => 8, 2 => 18, 3 => 14, 4 => 28] as $col => $width) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($col))->setWidth($width);
        }

        $sheet->freezePane('A2');
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setTitle($this->title());
            },
        ];
    }

    abstract public function title(): string;
}
