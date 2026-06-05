<?php declare(strict_types=1);

namespace App\Jobs\Billing;

use Core\App\Billing\Payment\SaveImportPaymentsCommand;
use Core\App\Billing\Payment\SaveImportPaymentsInput;
use Core\Domains\Billing\Events\ImportPaymentData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SaveImportPaymentsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param ImportPaymentData[] $paymentsData
     */
    public function __construct(
        private readonly array $paymentsData,
    )
    {
    }

    public function handle(SaveImportPaymentsCommand $command): void
    {
        $command->execute(new SaveImportPaymentsInput($this->paymentsData));
    }
}
