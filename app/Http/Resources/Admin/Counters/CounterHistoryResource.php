<?php

namespace App\Http\Resources\Admin\Counters;

use App\Http\Resources\AbstractResource;
use Core\Domains\Counter\Models\CounterHistoryDTO;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Enums\DateTimeFormat;

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
        return [
            'id'            => $this->counterHistory->getId(),
            'value'         => $this->counterHistory->getValue(),
            'date'          => $this->counterHistory->getDate()?->format(DateTimeFormat::DATE_VIEW_FORMAT),
            'file'          => $this->counterHistory->getFile(),
            'counterId'     => $this->counterHistory->getCounterId(),
            'counterNumber' => $this->counterHistory->getCounter()?->getNumber(),
            'accountId'     => $this->counterHistory->getCounter()?->getAccountId(),
            'accountNumber' => $this->counterHistory->getCounter()?->getAccount()?->getNumber(),
            'historyUrl'    => $this->counterHistory->getId()
                ? HistoryChangesLocator::route(
                    type: HistoryType::COUNTER,
                    primaryId: $this->counterHistory->getCounterId(),
                    referenceType: $this->counterHistory->getCounterId() ? null : HistoryType::COUNTER_HISTORY,
                    referenceId  : $this->counterHistory->getCounterId() ? null : $this->counterHistory->getId(),
                ) : null,
        ];
    }
}