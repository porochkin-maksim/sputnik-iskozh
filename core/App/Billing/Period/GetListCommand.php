<?php declare(strict_types=1);

namespace Core\App\Billing\Period;

use App\Models\Billing\Period;
use Core\Domains\Billing\Period\PeriodSearchResponse;
use Core\Domains\Billing\Period\PeriodSearcher;
use Core\Domains\Billing\Period\PeriodService;
use Core\Repositories\SearcherInterface;

readonly class GetListCommand
{
    public function __construct(
        private PeriodService $periodService,
    )
    {
    }

    public function execute(): PeriodSearchResponse
    {
        return $this->periodService->search(
            (new PeriodSearcher())
                ->setSortOrderProperty(Period::START_AT, SearcherInterface::SORT_ORDER_DESC)
                ->setSortOrderProperty(Period::END_AT, SearcherInterface::SORT_ORDER_DESC),
        );
    }
}
