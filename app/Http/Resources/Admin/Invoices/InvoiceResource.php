<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Invoices;

use App\Http\Resources\Admin\Accounts\AccountResource;
use lc;
use App\Http\Resources\AbstractResource;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Billing\Invoice\Models\InvoiceDTO;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Resources\RouteNames;
use Core\Responses\ResponsesEnum;

readonly class InvoiceResource extends AbstractResource
{
    public function __construct(
        private InvoiceDTO $invoice,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access = lc::roleDecorator();

        return [
            'id'            => $this->invoice->getId(),
            'periodId'      => $this->invoice->getPeriodId(),
            'periodName'    => $this->invoice->getPeriod()?->getName(),
            'accountId'     => $this->invoice->getAccountId(),
            'accountNumber' => $this->invoice->getAccount()?->getNumber(),
            'type'          => $this->invoice->getType()?->value,
            'typeName'      => $this->invoice->getType()?->name(),
            'cost'          => $this->invoice->getCost(),
            'payed'         => $this->invoice->getPayed(),
            'delta'         => $this->invoice->getCost() - $this->invoice->getPayed(),
            'isPayed'       => $this->invoice->isPayed(),
            'created'       => $this->formatCreatedAt($this->invoice->getCreatedAt()),
            'actions'       => [
                ResponsesEnum::VIEW => $access->can(PermissionEnum::INVOICES_VIEW),
                ResponsesEnum::EDIT => $access->can(PermissionEnum::INVOICES_EDIT),
                ResponsesEnum::DROP => $access->can(PermissionEnum::INVOICES_DROP),
                'claims'            => [
                    ResponsesEnum::VIEW => $access->can(PermissionEnum::CLAIMS_VIEW),
                    ResponsesEnum::EDIT => $access->can(PermissionEnum::CLAIMS_EDIT),
                    ResponsesEnum::DROP => $access->can(PermissionEnum::CLAIMS_DROP),
                ],
                'payments'          => [
                    ResponsesEnum::VIEW => $access->can(PermissionEnum::PAYMENTS_VIEW),
                    ResponsesEnum::EDIT => $access->can(PermissionEnum::PAYMENTS_EDIT),
                    ResponsesEnum::DROP => $access->can(PermissionEnum::PAYMENTS_DROP),
                ],
            ],
            'viewUrl'       => $this->invoice->getId() ? route(RouteNames::ADMIN_INVOICE_VIEW, ['id' => $this->invoice->getId()]) : null,
            'historyUrl'    => $this->invoice->getId() ? route(RouteNames::HISTORY_CHANGES, [
                'type'      => HistoryType::INVOICE,
                'primaryId' => $this->invoice->getId(),
            ]) : null,
            'accountUrl'    => $this->invoice->getAccountId() && $access->can(PermissionEnum::ACCOUNTS_VIEW)
                ? route(RouteNames::ADMIN_ACCOUNT_VIEW, ['accountId' => $this->invoice?->getAccountId()])
                : null,
            'account' => $this->invoice->getAccount() ? new AccountResource($this->invoice->getAccount()) : null,
        ];
    }
}
