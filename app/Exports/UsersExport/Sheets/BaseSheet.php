<?php declare(strict_types=1);

namespace App\Exports\UsersExport\Sheets;

use Core\Domains\User\Collections\UserCollection;
use Core\Domains\User\UserLocator;
use Core\Enums\DateTimeFormat;
use Core\Helpers\Phone\PhoneHelper;
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
    public const string ID              = 'id';
    public const string ACCOUNT         = 'account';
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
        $result = [];

        foreach ($this->users as $user) {
            $row      = array_fill_keys(array_keys($this->headers), 0);
            $exData   = $user->getExData();
            $accounts = $user->getAccounts();

            $row[self::ID]              = $user->getId();
            $row[self::ACCOUNT]         = implode(', ', $accounts->getNumbersWitchFraction());
            $row[self::NAME]            = UserLocator::UserDecorator($user)->getFullName();
            $row[self::EMAIL]           = $user->getEmailVerifiedAt() ? $user->getEmail() : null;
            $row[self::PHONE]           = $user->getPhone() ? PhoneHelper::normalizePhone($user->getPhone()) : null;
            $row[self::MEMBERSHIP]      = $user->getOwnershipDate()?->format(DateTimeFormat::DATE_VIEW_FORMAT);
            $row[self::MEMBERSHIP_DUTY] = $user->getOwnershipDutyInfo();
            $row[self::ADD_PHONE]       = $exData->getPhone();
            $row[self::ADDRESS]         = $exData->getLegalAddress();
            $row[self::POST_ADDRESS]    = $exData->getPostAddress();
            $row[self::NOTE]            = $exData->getAdditional();

            if ($accounts->first()) {
                $result[$accounts->first()->getSortValue()] = $row;
            }
            else {
                $result[] = $row;
            }
        }

        ksort($result);

        return collect($result);
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
                'startColor' => [
                    'rgb' => '4472C4',
                ],
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

        // Применяем стили к заголовкам
        $sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray($headerStyle);

        // Применяем стили к данным
        $sheet->getStyle('A2:' . $lastColumn . $lastRow)->applyFromArray($dataStyle);

        // Устанавливаем фильтр
        $sheet->setAutoFilter('A1:' . $lastColumn . $lastRow);

        // Автоматическая ширина столбцов
        foreach (range('A', $lastColumn) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Замораживаем первую строку
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