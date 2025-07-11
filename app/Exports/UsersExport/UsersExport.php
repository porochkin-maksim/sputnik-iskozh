<?php declare(strict_types=1);

namespace App\Exports\UsersExport;

use App\Exports\UsersExport\Sheets\BaseSheet;
use App\Exports\UsersExport\Sheets\UsersPlotSheet;
use Core\Domains\User\Collections\UserCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class UsersExport implements WithMultipleSheets
{
    private array $headers;
    private array $groupedUsers = [];

    private const PLOT_MEMBER = 'Пользователи';

    public function __construct(
        private readonly UserCollection $users,
    )
    {
        $this->headers = [
            BaseSheet::ID              => 'ID',
            BaseSheet::ACCOUNT         => 'Участок',
            BaseSheet::NAME            => 'ФИО',
            BaseSheet::EMAIL           => 'Почта',
            BaseSheet::PHONE           => 'Телефон',
            BaseSheet::MEMBERSHIP      => 'Членство',
            BaseSheet::MEMBERSHIP_DUTY => 'Основание членства',
            BaseSheet::ADD_PHONE       => 'Доп. телефон',
            BaseSheet::ADDRESS         => 'Адрес регистрации',
            BaseSheet::POST_ADDRESS    => 'Почтовый адрес',
            BaseSheet::NOTE            => 'Примечание',
        ];

        $this->groupedUsers[self::PLOT_MEMBER] = $this->users;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->groupedUsers as $plotNumber => $users) {
            $sheets[] = new UsersPlotSheet($users, $this->headers, $plotNumber);
        }

        return $sheets;
    }
}
