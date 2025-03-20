<?php

namespace App\Http\Resources\Admin\Counters;

use App\Http\Resources\AbstractResource;
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

        return [
            'id'            => $this->counterHistory->getId(),
            'value'         => $this->counterHistory->getValue(),
            'previousValue' => $this->counterHistory->getPreviousValue(),
            'date'          => $this->counterHistory->getDate()?->format(DateTimeFormat::DATE_VIEW_FORMAT),
            'file'          => $this->counterHistory->getFile(),
            'counterId'     => $this->counterHistory->getCounterId(),
            'counterNumber' => $counter?->getNumber(),
            'accountId'     => $counter?->getAccountId(),
            'accountNumber' => $counter?->getAccount()?->getNumber(),
            'isInvoicing'   => $counter?->isInvoicing(),
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
    }
}