<?php declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\Common\AccountsSelectResource;
use App\Models\Account\Account;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Models\AccountSearcher;
use Illuminate\Http\JsonResponse;

class SelectsController extends Controller
{
    public function accounts(): JsonResponse
    {
        $searcher = new AccountSearcher()
            ->setWithoutSntAccount()
            ->setSortOrderProperty(Account::SORT_VALUE, SearcherInterface::SORT_ORDER_ASC)
        ;
        $items = AccountLocator::AccountService()->search($searcher);

        return response()->json(new AccountsSelectResource(
            $items->getItems(),
            false,
        ));
    }
}