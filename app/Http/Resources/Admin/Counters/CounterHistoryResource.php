<?php

namespace App\Http\Resources\Admin\Counters;

use App\Http\Resources\AbstractResource;
use App\Http\Resources\Admin\Claims\ClaimResource;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Counter\Models\CounterHistoryDTO;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Enums\DateTimeFormat;
use Core\Resources\RouteNames;

readonly class CounterHistoryResource extends AbstractResource
{
    public function __construct(
        private CounterHistoryDTO  $counterHistory,
        private ?CounterHistoryDTO $previousCounterHistory = null,
    )
    {
    }

    public function jsonSerialize(): array
    {
        $access  = \lc::roleDecorator();
        $counter = $this->counterHistory->getCounter();

        $claim         = $this->counterHistory->getClaim();
        $previousValue = $this->previousCounterHistory?->getValue() ? : $this->counterHistory->getPreviousValue();

        $result = [
            'id'            => $this->counterHistory->getId(),
            'counterId'     => $this->counterHistory->getCounterId(),
            'value'         => $this->counterHistory->getValue(),
            'isVerified'    => $this->counterHistory->isVerified(),
            'before'        => $previousValue,
            'delta'         => $previousValue ? ($this->counterHistory->getValue() - $previousValue) : null,
            'date'          => $this->counterHistory->getDate()?->format(DateTimeFormat::DATE_DEFAULT),
            'days'          => $this?->previousCounterHistory ? $this->counterHistory->getDate()?->diffInDays($this?->previousCounterHistory->getDate()) : null,
            'file'          => $this->counterHistory->getFile(),
            'counterNumber' => $counter?->getNumber(),
            'accountId'     => $counter?->getAccountId(),
            'accountNumber' => $counter?->getAccount()?->getNumber(),
            'isInvoicing'   => $counter?->isInvoicing(),
            'claim'         => $claim ? new ClaimResource($claim) : null,
            'historyUrl'    => $this->counterHistory->getId()
                ? HistoryChangesLocator::route(
                    type         : HistoryType::COUNTER,
                    primaryId    : $this->counterHistory->getCounterId(),
                    referenceType: $this->counterHistory->getCounterId() ? null : HistoryType::COUNTER_HISTORY,
                    referenceId  : $this->counterHistory->getCounterId() ? null : $this->counterHistory->getId(),
                ) : null,
            'accountUrl'    => $counter?->getAccountId() && $access->can(PermissionEnum::ACCOUNTS_VIEW)
                ? route(RouteNames::ADMIN_ACCOUNT_VIEW, ['accountId' => $counter?->getAccountId()])
                : null,
        ];

        if ($claim && $access->can(PermissionEnum::INVOICES_VIEW)) {
            $result['invoiceUrl'] = route(RouteNames::ADMIN_INVOICE_VIEW, ['id' => $claim->getInvoiceId()]);
        }

        return $result;
    }
}