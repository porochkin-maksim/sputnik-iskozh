<?php declare(strict_types=1);

namespace App\Exports\UsersExport\Sheets;

use Core\Domains\User\Collections\UserCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class UsersPlotSheet extends BaseSheet implements WithTitle
{
    public function __construct(
        UserCollection|array    $users,
        array                   $headers,
        private readonly string $plotNumber,
    )
    {
        parent::__construct($users, $headers);
    }

    public function title(): string
    {
        return $this->plotNumber;
    }

    public function registerEvents(): array
    {
        $events = parent::registerEvents();

        $events[AfterSheet::class] = function (AfterSheet $event) {
            $sheet = $event->sheet->getDelegate();
            $sheet->setTitle($this->plotNumber);
        };

        return $events;
    }
} 