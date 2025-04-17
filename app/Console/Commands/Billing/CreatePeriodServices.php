<?php declare(strict_types=1);

namespace App\Console\Commands\Billing;

use Core\Domains\Billing\Jobs\CreateMainServicesJob;
use Core\Domains\Billing\Period\Models\PeriodSearcher;
use Core\Domains\Billing\Period\PeriodLocator;
use Illuminate\Console\Command;

class CreatePeriodServices extends Command
{
    protected $signature   = 'billing:period:create-services';
    protected $description = 'Creates services for each period if they doesnt exist';

    public function handle(): void
    {
        $periods = PeriodLocator::PeriodService()->search(PeriodSearcher::make())->getItems();

        foreach ($periods as $period) {
            CreateMainServicesJob::dispatchSync($period->getId());
        }
    }
} 