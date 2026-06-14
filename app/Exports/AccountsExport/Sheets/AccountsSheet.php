<?php declare(strict_types=1);

namespace App\Exports\AccountsExport\Sheets;

use Core\Domains\Account\AccountCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class AccountsSheet extends BaseSheet implements WithTitle
{
    public function __construct(
        AccountCollection|array $accounts,
        array                   $headers,
        private readonly string $title,
    )
    {
        parent::__construct($accounts, $headers);
    }

    public function title(): string
    {
        return $this->title;
    }

    public function registerEvents(): array
    {
        $events = parent::registerEvents();

        $events[AfterSheet::class] = function (AfterSheet $event) {
            $sheet = $event->sheet->getDelegate();
            $sheet->setTitle($this->title);
        };

        return $events;
    }
}
