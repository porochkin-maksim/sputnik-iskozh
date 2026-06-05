<?php declare(strict_types=1);

namespace App\Jobs\Billing;

use App\Services\Queue\QueueEnum;
use Core\App\Billing\Service\CreateMainServicesCommand;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * При создании периода создаем основные услуги
 */
class CreateMainServicesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $periodId,
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    public function handle(CreateMainServicesCommand $command): void
    {
        $command->execute($this->periodId);
    }
}
