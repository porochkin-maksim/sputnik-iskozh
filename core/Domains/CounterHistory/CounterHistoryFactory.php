<?php declare(strict_types=1);

namespace Core\Domains\CounterHistory;

use Carbon\Carbon;

class CounterHistoryFactory
{
    public function makeDefault(): CounterHistoryEntity
    {
        return (new CounterHistoryEntity())
            ->setIsVerified(false)
            ->setDate(Carbon::now());
    }
}
