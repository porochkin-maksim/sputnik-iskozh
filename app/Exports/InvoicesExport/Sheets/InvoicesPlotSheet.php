<?php declare(strict_types=1);

namespace App\Exports\InvoicesExport\Sheets;

use Core\Domains\Billing\Invoice\Collections\InvoiceCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class InvoicesPlotSheet extends BaseSheet implements WithTitle
{
    public function __construct(
        InvoiceCollection|array $invoices,
        array                   $headers,
        private int             $plotNumber,
    )
    {
        parent::__construct($invoices, $headers);
    }

    public function title(): string
    {
        return "Участок {$this->plotNumber}";
    }

    public function registerEvents(): array
    {
        $events = parent::registerEvents();

        $events[AfterSheet::class] = function (AfterSheet $event) {
            $sheet = $event->sheet->getDelegate();
            $sheet->setTitle("Участок {$this->plotNumber}");
        };

        return $events;
    }
} 