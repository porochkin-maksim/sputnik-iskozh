<?php

namespace Core\Domains\Counter\Listeners;

use Core\Domains\Billing\Claim\ClaimLocator;
use Core\Domains\Billing\ClaimToObject\Enums\ClaimObjectTypeEnum;
use Core\Domains\Billing\ClaimToObject\ClaimToObjectLocator;
use Core\Domains\Counter\CounterLocator;
use Core\Domains\Counter\Events\CounterHistoryDeletingEvent;
use Core\Domains\Counter\Jobs\DeleteCounterHistoryFileJob;
use Core\Domains\Counter\Jobs\RewatchCounterHistoryChainJob;
use Exception;
use Illuminate\Support\Facades\DB;

class CounterHistoryDeletingListener
{
    public function handle(CounterHistoryDeletingEvent $event): void
    {
        DB::beginTransaction();

        try {
            /**
             * Удалить связанную услугу, если есть
             */
            $claim = ClaimToObjectLocator::ClaimToObjectService()
                ->getByReference(ClaimObjectTypeEnum::COUNTER_HISTORY, $event->counterHistoryId)
            ;

            if ($claim) {
                ClaimLocator::ClaimService()->deleteById($claim->getId());
            }

            $history = CounterLocator::CounterHistoryService()->getById($event->counterHistoryId);
            if ( ! $history) {
                return;
            }

            dispatch(new RewatchCounterHistoryChainJob($history->getCounterId()));

            dispatch(new DeleteCounterHistoryFileJob($event->counterHistoryId));

            DB::commit();
        }
        catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}