<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Accounts\AccountsSelectResource;
use App\Http\Resources\Admin\Periods\PeriodsSelectResource;
use App\Http\Resources\Common\CountersSelectResource;
use App\Models\Account\Account;
use App\Models\Counter\Counter;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Models\AccountSearcher;
use Core\Domains\Billing\Period\Models\PeriodSearcher;
use Core\Domains\Billing\Period\PeriodLocator;
use Core\Domains\Counter\CounterLocator;
use Core\Domains\Counter\Models\CounterSearcher;
use Illuminate\Http\JsonResponse;

class SelectCollectionsController extends Controller
{
    public function periods(): JsonResponse
    {
        $searcher = new PeriodSearcher();
        $searcher->setSortOrderProperty(Account::SORT_VALUE, SearcherInterface::SORT_ORDER_ASC);
        $items = PeriodLocator::PeriodService()->search($searcher);

        return response()->json(new PeriodsSelectResource(
            $items->getItems(),
        ));
    }

    public function accounts(): JsonResponse
    {
        $searcher = new AccountSearcher();
        $searcher->setSortOrderProperty(Account::SORT_VALUE, SearcherInterface::SORT_ORDER_ASC);
        $items = AccountLocator::AccountService()->search($searcher);

        return response()->json(new AccountsSelectResource(
            $items->getItems(),
            false,
        ));
    }

    public function counters(int $accountId): JsonResponse
    {
        $searcher = new CounterSearcher();
        $searcher
            ->setAccountId($accountId)
            ->setSortOrderProperty(Counter::ACCOUNT_ID, SearcherInterface::SORT_ORDER_ASC)
        ;
        $items = CounterLocator::CounterService()->search($searcher);

        return response()->json(new CountersSelectResource(
            $items->getItems(),
            false,
        ));
    }
}