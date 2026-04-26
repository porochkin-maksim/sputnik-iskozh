<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Invoices;

use lc;
use App\Http\Resources\AbstractResource;
use App\Support\HistoryChangesRoute;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Billing\Invoice\InvoiceCollection;
use Core\Domains\HistoryChanges\HistoryType;

readonly class InvoicesListResource extends AbstractResource
{
    public function __construct(
        private InvoiceCollection $invoiceCollection,
        private ?int              $totalInvoicesCount = null,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access = lc::roleDecorator();
        $result = [
            'invoices'   => [],
            'total'      => $this->totalInvoicesCount,
            'historyUrl' => HistoryChangesRoute::make(
                type: HistoryType::INVOICE,
            ),
            'actions'    => [
                'view' => $access->can(PermissionEnum::INVOICES_VIEW),
                'edit' => $access->can(PermissionEnum::INVOICES_EDIT),
                'drop' => $access->can(PermissionEnum::INVOICES_DROP),
            ],
        ];

        foreach ($this->invoiceCollection as $invoice) {
            $result['invoices'][] = new InvoiceResource($invoice);
        }

        return $result;
    }
}
