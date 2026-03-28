<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\Accounts\AccountsSelectResource;
use App\Http\Resources\Admin\Periods\PeriodsSelectResource;
use App\Http\Resources\Common\CountersSelectResource;
use App\Http\Resources\Common\SelectResource;
use App\Models\Account\Account;
use App\Models\Billing\Period;
use App\Models\Counter\Counter;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Models\AccountSearcher;
use Core\Domains\Billing\Period\Models\PeriodSearcher;
use Core\Domains\Billing\Period\PeriodLocator;
use Core\Domains\Billing\Service\Enums\ServiceTypeEnum;
use Core\Domains\Counter\CounterLocator;
use Core\Domains\Counter\Models\CounterSearcher;
use Illuminate\Http\JsonResponse;

class SelectCollectionsController extends Controller
{
    public function periods(DefaultRequest $request): JsonResponse
    {
        $searcher = new PeriodSearcher()->setSortOrderProperty(Period::ID, SearcherInterface::SORT_ORDER_DESC);

        $items = PeriodLocator::PeriodService()->search(new PeriodSearcher()
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
        $items = AccountLocator::AccountService()->search($searcher);

        return response()->json(new AccountsSelectResource(
            $items->getItems(),
            false,
        ));
    }

    public function counters(?int $accountId): JsonResponse
    {
        $searcher = new CounterSearcher()->setSortOrderProperty(Counter::ACCOUNT_ID, SearcherInterface::SORT_ORDER_ASC);

        if ($accountId) {
            $searcher->setAccountId($accountId);
        }
        $items = CounterLocator::CounterService()->search($searcher);

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