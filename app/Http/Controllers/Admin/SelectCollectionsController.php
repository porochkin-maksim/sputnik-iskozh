<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\Periods\PeriodsSelectResource;
use App\Http\Resources\Common\AccountsSelectResource;
use App\Http\Resources\Common\CountersSelectResource;
use App\Http\Resources\Common\SelectResource;
use App\Models\Account\Account;
use App\Models\Billing\Period;
use App\Models\Counter\Counter;
use Core\Repositories\SearcherInterface;
use Core\Domains\Account\AccountSearcher;
use Core\Domains\Account\AccountService;
use Core\Domains\Billing\Period\PeriodSearcher;
use Core\Domains\Billing\Period\PeriodService;
use Core\Domains\Billing\Service\ServiceTypeEnum;
use Core\Domains\Counter\CounterSearcher;
use Core\Domains\Counter\CounterService;
use Illuminate\Http\JsonResponse;

class SelectCollectionsController extends Controller
{
    public function __construct(
        private readonly PeriodService  $periodService,
        private readonly AccountService $accountService,
        private readonly CounterService $counterService,
    )
    {
    }

    public function periods(DefaultRequest $request): JsonResponse
    {
        $items = $this->periodService->search((new PeriodSearcher())
            ->setSortOrderProperty(Period::ID, SearcherInterface::SORT_ORDER_DESC),
        )->getItems();

        return response()->json(new PeriodsSelectResource(
            $items,
        ));
    }

    public function accounts(): JsonResponse
    {
        $searcher = new AccountSearcher();
        $searcher->setSortOrderProperty(Account::SORT_VALUE, SearcherInterface::SORT_ORDER_ASC);
        $items = $this->accountService->search($searcher);

        return response()->json(new AccountsSelectResource(
            $items->getItems(),
            false,
        ));
    }

    public function counters(?int $accountId): JsonResponse
    {
        $searcher = (new CounterSearcher())->setSortOrderProperty(Counter::ACCOUNT_ID, SearcherInterface::SORT_ORDER_ASC);

        if ($accountId) {
            $searcher->setAccountId($accountId);
        }
        $items = $this->counterService->search($searcher);

        return response()->json(new CountersSelectResource(
            $items->getItems(),
            false,
        ));
    }

    public function servicesTypes(DefaultRequest $request): JsonResponse
    {
        $types = ServiceTypeEnum::array();

        return response()->json(new SelectResource($types));
    }
}
