<?php declare(strict_types=1);

namespace App\Observers\Billing;

use App\Models\Billing\Service;
use App\Observers\AbstractObserver;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;

class ServiceObserver extends AbstractObserver
{
    protected function getPrimaryHistoryType(): HistoryType
    {
        return HistoryType::SERVICE;
    }

    protected function getPropertyTitles(): array
    {
        return Service::PROPERTIES_TO_TITLES;
    }
}
