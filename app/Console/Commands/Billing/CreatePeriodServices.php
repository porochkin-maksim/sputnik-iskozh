<?php declare(strict_types=1);

namespace App\Console\Commands\Billing;

use App\Jobs\Billing\CreateMainServicesJob;
use Core\Domains\Billing\Period\PeriodSearcher;
use Core\Domains\Billing\Period\PeriodService;
use Illuminate\Console\Command;

class CreatePeriodServices extends Command
{
    protected $signature   = 'billing:period:create-services';
    protected $description = 'Creates services for each period if they doesnt exist';

    public function __construct(
        private readonly PeriodService $periodService,
    )
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $periods = $this->periodService->search(PeriodSearcher::make())->getItems();

        foreach ($periods as $period) {
            CreateMainServicesJob::dispatchSync($period->getId());
        }
    }
}
