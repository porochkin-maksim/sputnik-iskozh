<?php declare(strict_types=1);

namespace App\Exports\InvoicesExport\Sheets;

use Core\Domains\Billing\Invoice\Collections\InvoiceCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class InvoicesMainSheet extends BaseSheet implements WithTitle
{
    public function __construct(
        InvoiceCollection|array $invoices,
        array                   $headers,
    )
    {
        parent::__construct($invoices, $headers);
    }

    public function title(): string
    {
        return 'Все счета';
    }

    public function registerEvents(): array
    {
        $events = parent::registerEvents();

        $events[AfterSheet::class] = static function (AfterSheet $event) {
            $sheet = $event->sheet->getDelegate();
            $sheet->setTitle('Все счета');
        };

        return $events;
    }
} 