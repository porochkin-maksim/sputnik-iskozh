<?php declare(strict_types=1);

namespace App\Jobs\Billing;

use App\Services\Queue\QueueEnum;
use Core\App\Billing\Service\CreateOtherServiceCommand;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Создаёт технологическую услугу с типом "прочее" для указанного периода
 */
class CreateOtherServiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly int $periodId,
    )
    {
        $this->onQueue(QueueEnum::DEFAULT->value);
    }

    public function handle(CreateOtherServiceCommand $command): void
    {
        $command->execute($this->periodId);
    }
}
