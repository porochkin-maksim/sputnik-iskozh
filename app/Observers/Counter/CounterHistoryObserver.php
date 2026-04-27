<?php declare(strict_types=1);

namespace App\Observers\Counter;

use App\Models\Counter\CounterHistory;
use App\Observers\AbstractObserver;
use Core\Domains\Billing\Claim\ClaimLocator;
use Core\Domains\Billing\ClaimToObject\ClaimToObjectLocator;
use Core\Domains\Billing\ClaimToObject\Enums\ClaimObjectTypeEnum;
use Core\Domains\Counter\Jobs\NotifyAboutNewUnverifiedCounterHistoryJob;
use Core\Domains\Counter\Jobs\RewatchCounterHistoryChainJob;
use Core\Domains\File\Enums\FileTypeEnum;
use Core\Domains\File\Jobs\DeleteFilesJob;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Helpers\DateTime\DateTimeHelper;
use Illuminate\Database\Eloquent\Model;

class CounterHistoryObserver extends AbstractObserver
{
    protected function getPrimaryHistoryType(): HistoryType
    {
        return HistoryType::COUNTER;
    }

    protected function getPrimaryIdField(): ?string
    {
        return CounterHistory::COUNTER_ID;
    }

    protected function getReferenceHistoryType(): ?HistoryType
    {
        return HistoryType::COUNTER_HISTORY;
    }

    protected function getReferenceIdField(): ?string
    {
        return CounterHistory::ID;
    }

    protected function getPropertyTitles(): array
    {
        return CounterHistory::PROPERTIES_TO_TITLES;
    }

    /**
     * @var CounterHistory $item
     */
    public function created(Model $item): void
    {
        parent::created($item);

        if ( ! $item->is_verified) {
            NotifyAboutNewUnverifiedCounterHistoryJob::dispatchIfNeeded($item->id);
        }

        RewatchCounterHistoryChainJob::dispatchIfNeeded($item->counter_id);
    }

    /**
     * @var CounterHistory $item
     */
    public function updated(Model $item): void
    {
        parent::created($item);

        if (
            $item->value !== $item->getOriginal(CounterHistory::VALUE)
            || DateTimeHelper::toCarbonOrNull($item->date)?->format('d.m.Y') !== DateTimeHelper::toCarbonOrNull($item->getOriginal(CounterHistory::DATE))?->format('d.m.Y')
        ) {
            RewatchCounterHistoryChainJob::dispatchIfNeeded($item->counter_id);
        }
    }

    /**
     * @var CounterHistory $item
     */
    public function deleted(Model $item): void
    {
        parent::deleted($item);

        $claim = ClaimToObjectLocator::ClaimToObjectService()->getByReference(ClaimObjectTypeEnum::COUNTER_HISTORY, $item->id);

        if ($claim) {
            ClaimLocator::ClaimService()->deleteById($claim->getId());
        }

        DeleteFilesJob::dispatchIfNeeded($item->id, FileTypeEnum::COUNTER_HISTORY);
    }
}
