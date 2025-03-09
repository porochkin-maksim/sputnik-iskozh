<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Invoices;

use App\Http\Resources\AbstractResource;
use App\Http\Resources\Common\SelectOptionResource;
use App\Http\Resources\Common\SelectResource;
use Core\Domains\Account\Collections\AccountCollection;
use Core\Domains\Billing\Invoice\Collections\InvoiceCollection;
use Core\Domains\Billing\Invoice\Enums\InvoiceTypeEnum;
use Core\Domains\Billing\Period\Collections\PeriodCollection;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;

readonly class InvoicesListResource extends AbstractResource
{
    public function __construct(
        private InvoiceCollection $invoiceCollection,
        private int               $totalInvoicesCount,
        private PeriodCollection  $periodCollection,
        private AccountCollection $accountCollection,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $result = [
            'invoices'   => [],
            'total'      => $this->totalInvoicesCount,
            'types'      => new SelectResource(InvoiceTypeEnum::array()),
            'historyUrl' => HistoryChangesLocator::route(
                type: HistoryType::INVOICE,
            ),
        ];

        foreach ($this->invoiceCollection as $invoice) {
            $result['invoices'][] = new InvoiceResource($invoice);
        }

        foreach ($this->periodCollection as $period) {
            $result['periods'][] = new SelectOptionResource(
                $period->getId(),
                $period->getName(),
            );
        }

        foreach ($this->accountCollection as $account) {
            $result['accounts'][] = new SelectOptionResource(
                $account->getId(),
                $account->getNumber(),
            );
        }

        return $result;
    }
}
