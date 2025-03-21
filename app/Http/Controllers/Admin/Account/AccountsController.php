<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Accounts\ListRequest;
use App\Http\Requests\Admin\Accounts\SaveRequest;
use App\Http\Resources\Admin\Accounts\AccountResource;
use App\Http\Resources\Admin\Accounts\AccountsListResource;
use App\Models\Account\Account;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Factories\AccountFactory;
use Core\Domains\Account\Models\AccountSearcher;
use Core\Domains\Account\Services\AccountService;
use Core\Responses\ResponsesEnum;
use Illuminate\Http\JsonResponse;
use lc;

class AccountsController extends Controller
{
    private AccountFactory $accountFactory;
    private AccountService $accountService;

    public function __construct()
    {
        $this->accountFactory = AccountLocator::AccountFactory();
        $this->accountService = AccountLocator::AccountService();
    }

    public function create(): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::ACCOUNTS_EDIT)) {
            abort(403);
        }

        return response()->json(new AccountResource($this->accountFactory->makeDefault()));
    }

    public function view(int $id)
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::ACCOUNTS_VIEW)) {
            abort(403);
        }
        if ( ! $id && ! lc::roleDecorator()->can(PermissionEnum::ACCOUNTS_EDIT)) {
            abort(403);
        }
        $accountSearcher = new AccountSearcher();
        $accountSearcher
            ->setId($id)
            ->setWithUsers()
        ;
        $account = $this->accountService->search($accountSearcher)->getItems()->first();
        
        if ( ! $account) {
            abort(404);
        }
        $account = new AccountResource($account);

        return view('admin.pages.accounts.view', compact('account'));
    }

    public function list(ListRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::ACCOUNTS_VIEW)) {
            abort(403);
        }

        $searcher = new AccountSearcher();
        $searcher
            ->setLimit($request->getLimit())
            ->setOffset($request->getOffset())
            ->setSortOrderProperty(Account::NUMBER, SearcherInterface::SORT_ORDER_ASC);

        if ($request->getAccountId()) {
            $searcher->setId($request->getAccountId());
        }
        $accounts = $this->accountService->search($searcher);

        $searcher = new AccountSearcher();
        $searcher
            ->setSortOrderProperty(Account::NUMBER, SearcherInterface::SORT_ORDER_ASC);
        $allAccounts = $this->accountService->search($searcher);

        return response()->json(new AccountsListResource(
            $accounts->getItems(),
            $accounts->getTotal(),
            $allAccounts->getItems(),
        ));
    }

    public function save(SaveRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::ACCOUNTS_EDIT)) {
            abort(403);
        }

        $account = $request->getId()
            ? $this->accountService->getById($request->getId())
            : $this->accountFactory->makeDefault();

        $account->setIsVerified(true);

        if ( ! $account) {
            abort(404);
        }

        $account
            ->setNumber($request->getNumber())
            ->setSize($request->getSize())
            ->setIsMember($request->getIsMember());

        $account = $this->accountService->save($account);

        return response()->json([
            ResponsesEnum::ACCOUNT => new AccountResource($account),
        ]);
    }

    public function delete(int $id): bool
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::ACCOUNTS_DROP)) {
            abort(403);
        }

        return $this->accountService->deleteById($id);
    }
}
