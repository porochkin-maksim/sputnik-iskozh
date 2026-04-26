<?php declare(strict_types=1);

namespace App\Http\Resources\Admin\Invoices;

use App\Http\Resources\Admin\Accounts\AccountResource;
use App\Services\Money\MoneyService;
use lc;
use App\Http\Resources\AbstractResource;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\HistoryChanges\HistoryType;
use App\Resources\RouteNames;

readonly class InvoiceResource extends AbstractResource
{
    public function __construct(
        private InvoiceEntity $invoice,
    )
    {
    }

    public function getInvoice(): InvoiceEntity
    {
        return $this->invoice;
    }

    public function jsonSerialize(): array
    {
        $access = lc::roleDecorator();

        $period = $this->invoice->getPeriod();

        $claims = $this->invoice->getClaims();

        $detailCost = null;
        if ($claims) {
            $advance = $claims?->getAdvancePayment();
            $debts   = $claims?->getDebts();

            $advanceCost = MoneyService::parse((float) $advance?->getCost());
            $debtCost    = MoneyService::parse((float) $debts?->getCost());
            $invoiceCost = MoneyService::parse((float) $this->invoice?->getCost());

            $detailCost = [
                'total'   => MoneyService::toFloat($invoiceCost),
                'advance' => MoneyService::toFloat($advanceCost),
                'debt'    => MoneyService::toFloat($debtCost),
                'main'    => MoneyService::toFloat($invoiceCost->subtract($advanceCost)->subtract($debtCost)),
            ];
        }

        return [
            'id'            => $this->invoice->getId(),
            'periodId'      => $this->invoice->getPeriodId(),
            'periodName'    => $period?->getName(),
            'accountId'     => $this->invoice->getAccountId(),
            'accountNumber' => $this->invoice->getAccount()?->getNumber(),
            'type'          => $this->invoice->getType()?->value,
            'typeName'      => $this->invoice->getType()?->name(),
            'name'          => $this->invoice->getName(),
            'displayName'   => $this->invoice->getName() ? sprintf('%s (%s)', $this->invoice->getName(), $this->invoice->getType()?->name()) : $this->invoice->getType()?->name(),
            'cost'          => $this->invoice->getCost(),
            'paid'          => $this->invoice->getPaid(),
            'delta'         => $this->invoice->getCost() - $this->invoice->getPaid(),
            'advance'       => (float) $claims?->getAdvancePayment()?->getPaid(),
            'detailCost'    => $detailCost,
            'isPaid'        => $this->invoice->isPaid(),
            'created'       => $this->formatDateTimeForRender($this->invoice->getCreatedAt()),
            'updated'       => $this->formatDateTimeForRender($this->invoice->getUpdatedAt()),
            'actions'       => [
                'view' => $access->can(PermissionEnum::INVOICES_VIEW),
                'edit' => $access->can(PermissionEnum::INVOICES_EDIT) && ( ! $period || ! $period->isClosed()),
                'drop' => $access->can(PermissionEnum::INVOICES_DROP) && ( ! $period || ! $period->isClosed()),
                'claims'            => [
                    'view' => $access->can(PermissionEnum::CLAIMS_VIEW),
                    'edit' => $access->can(PermissionEnum::CLAIMS_EDIT) && ( ! $period || ! $period->isClosed()),
                    'drop' => $access->can(PermissionEnum::CLAIMS_DROP) && ( ! $period || ! $period->isClosed()),
                ],
                'payments'          => [
                    'view' => $access->can(PermissionEnum::PAYMENTS_VIEW),
                    'edit' => $access->can(PermissionEnum::PAYMENTS_EDIT) && ( ! $period || ! $period->isClosed()),
                    'drop' => $access->can(PermissionEnum::PAYMENTS_DROP) && ( ! $period || ! $period->isClosed()),
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
            'receiptUrl'    => $this->invoice->getAccountId() && $access->can(PermissionEnum::ACCOUNTS_VIEW)
                ? route(RouteNames::ADMIN_DOCUMENT_RECEIPT_INVOICE, ['id' => $this->invoice?->getId()])
                : null,
            'account'       => $this->invoice->getAccount() ? new AccountResource($this->invoice->getAccount()) : null,
        ];
    }
}
