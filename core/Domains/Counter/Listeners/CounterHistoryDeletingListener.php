<?php

namespace Core\Domains\Counter\Listeners;

use Core\Domains\Billing\Transaction\TransactionLocator;
use Core\Domains\Billing\TransactionToObject\Enums\TransactionObjectTypeEnum;
use Core\Domains\Billing\TransactionToObject\TransactionToObjectLocator;
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
             * Удалить связанную транзакцию, если есть
             */
            $transaction = TransactionToObjectLocator::TransactionToObjectService()
                ->getByReference(TransactionObjectTypeEnum::COUNTER_HISTORY, $event->counterHistoryId)
            ;

            if ($transaction) {
                TransactionLocator::TransactionService()->deleteById($transaction->getId());
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