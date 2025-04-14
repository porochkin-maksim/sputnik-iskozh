<?php declare(strict_types=1);

namespace App\Exports;

use Core\Domains\Billing\Claim\Models\ClaimDTO;
use Core\Domains\Billing\Invoice\Collections\InvoiceCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;

class InvoicesExport implements FromArray, ShouldAutoSize, WithEvents
{
    private array $headers;

    public function __construct(
        private InvoiceCollection $invoices,
    )
    {
        $headers = [
            'id'      => '№',
            'period'  => 'Период',
            'account' => 'Участок',
            'type'    => 'Тип счёта',
            'cost'    => 'Начислено',
        ];

        $claimHeaders = [];
        foreach ($this->invoices as $invoice) {
            foreach ($invoice->getClaims() as $claim) {
                $claimName = $this->getClaimName($claim);
                if ( ! in_array($claimName, $headers, true)) {
                    $claimHeaders[$claimName] = $claimName;
                }
            }
        }

        foreach ($claimHeaders as $key => $claimHeader) {
            $headers[$key . 'cost'] = $claimHeader;
        }

        $headers['payed'] = 'Оплачено';

        foreach ($claimHeaders as $key => $claimHeader) {
            $headers[$key . 'payed'] = $claimHeader;
        }

        $headers['delta'] = 'Долг';

        $this->headers = $headers;
    }

    public function array(): array
    {
        $result = [];

        $headers  = $this->headers;
        $result[] = $this->headers;

        foreach ($this->invoices as $invoice) {
            $row = array_map(static fn($method) => 0, $headers);

            $row['id']      = $invoice->getId();
            $row['period']  = $invoice->getPeriod()?->getName();
            $row['account'] = $invoice->getAccount()?->getNumber();
            $row['type']    = $invoice->getType()?->name();
            $row['cost']    = $invoice->getCost();
            $row['payed']   = $invoice->getPayed();
            $row['delta']   = $invoice->getCost() - $invoice->getPayed();

            foreach ($invoice->getClaims() as $claim) {
                $row[$this->getClaimName($claim) . 'cost']  = $claim->getCost();
                $row[$this->getClaimName($claim) . 'payed'] = $claim->getPayed();
            }

            $result[] = $row;
        }

        return $result;
    }

    public function registerEvents(): array
    {
        return [
            // AfterSheet::class => function(AfterSheet $event) {
            //     $cellRange = "A1:W1";
            //     $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            // },
        ];
    }

    private function getClaimName(ClaimDTO $claim): ?string
    {
        return $claim->getService()?->getName() ? : $claim->getService()?->getType()?->name();
    }
}
