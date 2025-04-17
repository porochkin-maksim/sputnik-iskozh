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
use Core\Domains\Infra\ExData\Enums\ExDataTypeEnum;
use Core\Domains\Infra\ExData\ExDataLocator;
use Core\Domains\Infra\ExData\Services\ExDataService;
use Core\Responses\ResponsesEnum;
use Illuminate\Http\JsonResponse;
use lc;

class AccountsController extends Controller
{
    private AccountFactory $accountFactory;
    private AccountService $accountService;
    private ExDataService  $exDataService;

    public function __construct()
    {
        $this->accountFactory = AccountLocator::AccountFactory();
        $this->accountService = AccountLocator::AccountService();
        $this->exDataService  = ExDataLocator::ExDataService();
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

    public function get(int $id): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::ACCOUNTS_VIEW)) {
            abort(403);
        }
        if ( ! $id && ! lc::roleDecorator()->can(PermissionEnum::ACCOUNTS_EDIT)) {
            abort(403);
        }
        $account = $this->accountService->getById($id);

        return response()->json(new AccountResource($account));
    }

    public function list(ListRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::ACCOUNTS_VIEW)) {
            abort(403);
        }

        $searcher = AccountSearcher::make()
            ->setLimit($request->getLimit())
            ->setOffset($request->getOffset())
            ->setSortOrderProperty(Account::SORT_VALUE, SearcherInterface::SORT_ORDER_ASC)
        ;

        if ($request->getSearch()) {
            $searcher->addWhere(Account::NUMBER, SearcherInterface::LIKE, "%{$request->getSearch()}%");
        }

        if ($request->getAccountId()) {
            $searcher->setId($request->getAccountId());
        }
        $accounts = $this->accountService->search($searcher);

        $allAccounts = $this->accountService->search(
            AccountSearcher::make()->setSortOrderProperty(Account::NUMBER, SearcherInterface::SORT_ORDER_ASC),
        );

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

        if ( ! $account) {
            abort(404);
        }

        $account->setIsVerified(true);

        $account
            ->setNumber($request->getNumber())
            ->setIsInvoicing($request->getIsInvoicing())
            ->setSize($request->getSize())
        ;

        $account = $this->accountService->save($account);

        $exData = $this->exDataService->getByTypeAndReferenceId(ExDataTypeEnum::ACCOUNT, $account->getId())
            ? : $this->exDataService->makeDefault()->setType(ExDataTypeEnum::ACCOUNT)->setReferenceId($account->getId());

        $exData->setData($account->getExData()
            ->setCadastreNumber($request->getCadastreNumber())
            ->setRegistryDate($request->getRegistryDate())
            ->jsonSerialize(),
        );

        $this->exDataService->save($exData);

        return response()->json([
            ResponsesEnum::ACCOUNT => new AccountResource($this->accountService->getById($account->getId())),
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
