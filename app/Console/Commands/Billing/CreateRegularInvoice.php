<?php declare(strict_types=1);

namespace App\Console\Commands\Billing;

use Core\App\Billing\Invoice\CreateRegularPeriodInvoicesCommand;
use Core\App\Billing\Invoice\CreateRegularPeriodInvoicesInput;
use Illuminate\Console\Command;

class CreateRegularInvoice extends Command
{
    public function __construct(
        private readonly CreateRegularPeriodInvoicesCommand $command,
    )
    {
        parent::__construct();
    }

    protected $signature = 'billing:invoice:create-regular
                                {--account= : ID участника}
                                {--period= : ID периода}';
    protected $description = 'Создаёт регулярный счёт для участника за период';

    public function handle(): void
    {
        $accountId = (int) $this->option('account');
        $periodId  = (int) $this->option('period');

        if ( ! $accountId || ! $periodId) {
            $this->error('Необходимо указать --account и --period');

            return;
        }

        $this->info("Создание регулярного счёта для участка #{$accountId} за период #{$periodId}...");

        try {
            $this->command->execute(new CreateRegularPeriodInvoicesInput($periodId, [$accountId]));
            $this->info('Счёт создан.');
        }
        catch (\Throwable $e) {
            $this->error("Ошибка: " . $e->getMessage());
        }
    }
}
