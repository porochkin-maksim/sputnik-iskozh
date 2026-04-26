<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\Accounts\AccountResource;
use App\Models\Account\Account;
use App\Models\Billing\Payment;
use Core\Repositories\SearcherInterface;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Account\AccountSearcher;
use Core\Domains\Account\AccountService;
use Core\Domains\Billing\Payment\PaymentSearcher;
use Core\Domains\Billing\Payment\PaymentService;
use App\Resources\RouteNames;
use Illuminate\Http\JsonResponse;
use lc;

class TopPanelController extends Controller
{

    public function __construct(
        private readonly AccountService $accountService,
        private readonly PaymentService $paymentService,
    )
    {
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
            $searcher = (new PaymentSearcher())
                ->setInvoiceId(null)
                ->setWithFiles()
                ->setSortOrderProperty(Payment::ID, SearcherInterface::SORT_ORDER_ASC)
            ;

            $result['payments'] = $this->paymentService->search($searcher)->getItems()->count();
        }

        return response()->json($result);
    }

    public function search(DefaultRequest $request): ?string
    {
        $accountSearch = $request->getStringOrNull('account');
        $userSearch    = $request->getStringOrNull('user');

        $result = null;
        if ($accountSearch) {
            $accounts = $this->accountService->search(
                AccountSearcher::make()->addWhere(Account::NUMBER, SearcherInterface::EQUALS, $accountSearch),
            )->getItems();

            if ($accounts->count() === 1) {
                $result = (new AccountResource($accounts->first()))->getViewUrl();
            }
            elseif ($accounts->count() > 1) {
                $result = route(RouteNames::ADMIN_ACCOUNT_INDEX, ['search' => $accountSearch]);
            }
            else {
                $result = route(RouteNames::ADMIN_ACCOUNT_INDEX, ['search' => $accountSearch]);
            }
        }
        elseif ($userSearch) {
            $result = route(RouteNames::ADMIN_USER_INDEX, ['search' => $userSearch]);
        }

        return $result;
    }
}
