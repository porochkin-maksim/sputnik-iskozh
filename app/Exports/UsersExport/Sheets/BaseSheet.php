<?php declare(strict_types=1);

namespace App\Exports\UsersExport\Sheets;

use Core\Domains\User\UserCollection;
use Core\Shared\Helpers\DateTime\DateTimeFormat;
use Core\Shared\Helpers\Phone\PhoneHelper;
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
    public const string ID              = 'id';
    public const string ACCOUNT         = 'account';
    public const string FRACTION        = 'fraction';
    public const string NAME            = 'name';
    public const string EMAIL           = 'email';
    public const string PHONE           = 'phone';
    public const string MEMBERSHIP      = 'membership';
    public const string MEMBERSHIP_DUTY = 'membership_duty';
    public const string ADD_PHONE       = 'add_phone';
    public const string ADDRESS         = 'address';
    public const string POST_ADDRESS    = 'post_address';
    public const string NOTE            = 'note';

    public function __construct(
        protected UserCollection|array $users,
        protected array                $headers,
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

        foreach ($this->users as $user) {
            $base     = array_fill_keys(array_keys($this->headers), null);
            $exData   = $user->getExData();
            $accounts = $user->getAccounts();

            $base[self::ID]              = $user->getId();
            $base[self::NAME]            = $user->getViewer()->getFullName();
            $base[self::EMAIL]           = $user->getEmail();
            $base[self::PHONE]           = $user->getPhone() ? PhoneHelper::normalizePhone($user->getPhone()) : null;
            $base[self::MEMBERSHIP]      = $user->getMembershipDate()?->format(DateTimeFormat::DATE_VIEW_FORMAT);
            $base[self::MEMBERSHIP_DUTY] = $user->getMembershipDutyInfo();
            $base[self::ADD_PHONE]       = $exData->getPhone();
            $base[self::ADDRESS]         = $exData->getLegalAddress();
            $base[self::POST_ADDRESS]    = $exData->getPostAddress();
            $base[self::NOTE]            = $exData->getAdditional();

            $accountList = $accounts->toArray();
            if ($accountList) {
                foreach ($accountList as $account) {
                    $row = $base;
                    $row[self::ACCOUNT]  = $account->getNumber();
                    $row[self::FRACTION] = $account->getFractionPercent();
                    $rows[] = ['sort' => $account->getSortValue() ?? '', 'data' => $row];
                }
            }
            else {
                $rows[] = ['sort' => '', 'data' => $base];
            }
        }

        usort($rows, static fn($a, $b) => $a['data'][self::ID] - $b['data'][self::ID]);

        return collect(array_column($rows, 'data'));
    }

    public function styles(Worksheet $sheet): void
    {
        $lastColumn = $sheet->getHighestColumn();
        $lastRow    = $sheet->getHighestRow();

        // Стили для заголовков
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

        // Стили для данных
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

        // Striped rows
        $stripedColor = 'F5F5F5';
        for ($r = 2; $r <= $lastRow; $r += 2) {
            $sheet->getStyle("A{$r}:{$lastColumn}{$r}")
                ->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB($stripedColor);
        }

        // Выравнивание
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G2:G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F2:F' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('C2:C' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Wrap text для адресов
        $sheet->getStyle('K2:L' . $lastRow)->getAlignment()->setWrapText(true);

        // Фильтр
        $sheet->setAutoFilter('A1:' . $lastColumn . $lastRow);

        // Фиксированная ширина столбцов
        foreach ([1 => 8, 2 => 18, 3 => 10, 4 => 30, 5 => 28, 6 => 16, 7 => 14, 8 => 25, 9 => 16, 10 => 30, 11 => 30, 12 => 30] as $col => $width) {
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
