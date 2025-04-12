<?php declare(strict_types=1);

namespace Core\Domains\Account\Jobs;

use Core\Domains\Account\AccountLocator;
use Core\Queue\QueueEnum;
use Core\Services\Money\MoneyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateSntBalanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly float $difference,
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    public function handle(): void
    {
        return;
        $snt = AccountLocator::AccountService()->getSntAccount();
        if ($snt) {
            $diff    = MoneyService::parse($this->difference);
            $balance = MoneyService::parse($snt->getBalance());

            $balance = $balance->add($diff);

            $snt->setBalance(MoneyService::toFloat($balance));
            AccountLocator::AccountService()->save($snt);
        }
    }
}
