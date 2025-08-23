<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\Accounts\AccountResource;
use App\Http\Resources\Common\SelectOptionResource;
use App\Models\Account\Account;
use App\Models\Billing\Payment;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Models\AccountSearcher;
use Core\Domains\Account\Services\AccountService;
use Core\Domains\Billing\Payment\Models\PaymentSearcher;
use Core\Domains\Billing\Payment\PaymentLocator;
use Core\Domains\Billing\Payment\Services\PaymentService;
use Core\Resources\RouteNames;
use Illuminate\Http\JsonResponse;
use lc;

class TopPanelController extends Controller
{
    private AccountService $accountService;
    private PaymentService $paymentService;

    public function __construct()
    {
        $this->accountService = AccountLocator::AccountService();
        $this->paymentService = PaymentLocator::PaymentService();
    }

    public function index(): JsonResponse
    {
        $roleDecorator = lc::roleDecorator();
        $canActions    = false;
        $result        = [
            'actions'  => [
                'users'    => $roleDecorator->can(PermissionEnum::USERS_VIEW),
                'accounts' => $roleDecorator->can(PermissionEnum::ACCOUNTS_VIEW),
            ],
            'payments' => 0,
        ];
        foreach ($result['actions'] as $action) {
            $canActions = $canActions || $action;
        }
        $result['canActions'] = $canActions;

        if ($roleDecorator->can(PermissionEnum::PAYMENTS_VIEW)) {
            $searcher = new PaymentSearcher()
                ->setInvoiceId(null)
                ->setWithFiles()
                ->setSortOrderProperty(Payment::ID, SearcherInterface::SORT_ORDER_ASC)
            ;

            $result['payments'] = $this->paymentService->search($searcher)->getItems()->count();
        }

        return response()->json($result);
    }

    public function search(DefaultRequest $request): JsonResponse
    {
        $accountSearch = $request->getStringOrNull('account');
        $userSearch    = $request->getStringOrNull('user');

        $result = null;
        if ($accountSearch) {
            $accounts = $this->accountService->search(
                AccountSearcher::make()->addWhere(Account::NUMBER, SearcherInterface::EQUALS, $accountSearch),
            )->getItems();

            if ($accounts->count() === 1) {
                $result = new AccountResource($accounts->first())->getViewUrl();
            }
            elseif ($accounts->count() > 1) {
                $result = route(RouteNames::ADMIN_ACCOUNT_INDEX, ['search' => $accountSearch]);
            }
        }
        elseif ($userSearch) {
            $result = route(RouteNames::ADMIN_USER_INDEX, ['search' => $userSearch]);
        }

        return response()->json($result);
    }
}