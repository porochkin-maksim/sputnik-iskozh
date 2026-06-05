<?php declare(strict_types=1);

namespace App\Jobs\Billing;

use App\Services\Queue\QueueEnum;
use Core\App\Billing\Payment\NotifyAboutNewUnverifiedPaymentCommand;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyAboutNewUnverifiedPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $paymentId,
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    public function handle(NotifyAboutNewUnverifiedPaymentCommand $command): void
    {
        $command->execute($this->paymentId);
    }
}
