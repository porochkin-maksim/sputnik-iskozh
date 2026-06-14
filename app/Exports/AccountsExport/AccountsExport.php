<?php declare(strict_types=1);

namespace App\Exports\AccountsExport;

use App\Exports\AccountsExport\Sheets\AccountsSheet;
use App\Exports\AccountsExport\Sheets\BaseSheet;
use Core\Domains\Account\AccountCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AccountsExport implements WithMultipleSheets
{
    private array $headers;

    private const string SHEET_TITLE = 'Участки';

    public function __construct(
        private readonly AccountCollection $accounts,
    )
    {
        $this->headers = [
            BaseSheet::ID       => 'ID',
            BaseSheet::NUMBER   => 'Участок',
            BaseSheet::SIZE     => 'Площадь',
            BaseSheet::CADASTRE => 'Кадастровый номер',
        ];
    }

    public function sheets(): array
    {
        return [
            new AccountsSheet($this->accounts, $this->headers, self::SHEET_TITLE),
        ];
    }
}
