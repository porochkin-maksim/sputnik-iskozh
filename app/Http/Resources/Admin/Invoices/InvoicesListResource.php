<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Invoices;

use lc;
use App\Http\Resources\AbstractResource;
use App\Http\Resources\Common\SelectResource;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Billing\Invoice\Collections\InvoiceCollection;
use Core\Domains\Billing\Invoice\Enums\InvoiceTypeEnum;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Responses\ResponsesEnum;

readonly class InvoicesListResource extends AbstractResource
{
    public function __construct(
        private InvoiceCollection $invoiceCollection,
        private int               $totalInvoicesCount,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access = lc::roleDecorator();
        $result = [
            'invoices'   => [],
            'total'      => $this->totalInvoicesCount,
            'types'      => new SelectResource(InvoiceTypeEnum::array()),
            'historyUrl' => HistoryChangesLocator::route(
                type: HistoryType::INVOICE,
            ),
            'actions'    => [
                ResponsesEnum::VIEW => $access->can(PermissionEnum::INVOICES_VIEW),
                ResponsesEnum::EDIT => $access->can(PermissionEnum::INVOICES_EDIT),
                ResponsesEnum::DROP => $access->can(PermissionEnum::INVOICES_DROP),
            ],
        ];

        foreach ($this->invoiceCollection as $invoice) {
            $result['invoices'][] = new InvoiceResource($invoice);
        }

        return $result;
    }
}
