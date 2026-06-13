<?php declare(strict_types=1);

namespace App\Exports\InvoicesExport;

use App\Exports\InvoicesExport\Sheets\ClaimsSheet;
use App\Exports\InvoicesExport\Sheets\DetailSheet;
use App\Exports\InvoicesExport\Sheets\InvoicesSheet;
use App\Exports\InvoicesExport\Sheets\PaymentsSheet;
use App\Exports\InvoicesExport\Sheets\SummarySheet;
use Core\Domains\Billing\Invoice\InvoiceCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class InvoicesExport implements WithMultipleSheets
{
    public function __construct(
        private InvoiceCollection $invoices,
    )
    {
    }

    public function sheets(): array
    {
        // Сортируем счета по номеру участка
        $sortedInvoices = $this->invoices->toArray();
        usort($sortedInvoices, static function ($a, $b) {
            $aNumber = $a->getAccount()?->getSortValue() ?? '';
            $bNumber = $b->getAccount()?->getSortValue() ?? '';

            return strnatcmp($aNumber, $bNumber);
        });

        return [
            new SummarySheet($sortedInvoices),
            new InvoicesSheet($sortedInvoices),
            new DetailSheet($sortedInvoices),
            new ClaimsSheet($sortedInvoices),
            new PaymentsSheet($sortedInvoices),
        ];
    }
}
