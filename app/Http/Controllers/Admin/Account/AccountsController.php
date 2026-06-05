<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\AccountResource;
use App\Http\Resources\Common\AccountsSelectResource;
use App\Http\Resources\Shared\ResourseList;
use App\Resources\RouteNames;
use App\Support\HistoryChangesRoute;
use Core\App\Account\GetListCommand;
use Core\App\Account\SaveCommand;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Account\AccountFactory;
use Core\Domains\Account\AccountSearcher;
use Core\Domains\Account\AccountService;
use Core\Domains\HistoryChanges\HistoryType;
use Core\Exceptions\ValidationException;
use Illuminate\Http\JsonResponse;
use lc;

class AccountsController extends Controller
{
    public function __construct(
        private readonly AccountFactory $accountFactory,
        private readonly AccountService $accountService,
        private readonly GetListCommand $getListCommand,
        private readonly SaveCommand    $saveCommand,
    )
    {
    }

    // blade: resources/views/admin/pages/accounts.blade.php
    // vue: resources/js/components/admin/accounts/AccountsBlock.vue
    public function index()
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::ACCOUNTS_VIEW)) {
            abort(403);
        }

        return view('pages.admin.accounts.index');
    }

    public function create(): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::ACCOUNTS_EDIT)) {
            abort(403);
        }

        return response()->json(new AccountResource($this->accountFactory->makeDefault()));
    }

    // blade: resources/views/admin/pages/accounts/view.blade.php
    // vue: resources/js/components/admin/accounts/AccountItemView.vue
    // vue: resources/js/components/admin/accounts/AccountItemAdd.vue
    public function view(int $id)
    {
        $account = $this->getAccount($id);

        if ( ! $account) {
            abort(404);
        }

        foreach ($account->getUsers() as $user) {
            $user->setAccount($account);
        }

        return view('pages.admin.accounts.view', compact('account'));
    }

    public function get(int $id): JsonResponse
    {
        $account = $this->getAccount($id);

        return response()->json(new AccountResource($account));
    }

    /**
     * @throws ValidationException
     */
    // vue: resources/js/components/admin/accounts/AccountsBlock.vue
    public function list(DefaultRequest $request): JsonResponse
    {
        $roleDecorator = lc::roleDecorator();

        if ( ! $roleDecorator->can(PermissionEnum::ACCOUNTS_VIEW)) {
            abort(403);
        }

        $result = $this->getListCommand->execute(
            $request->getLimit(),
            $request->getOffset(),
            $request->getSearch(),
            $request->getIntOrNull('account_id'),
            $request->getSortField(),
            $request->getSortOrder(),
        );

        return response()->json([
            'accounts'    => new ResourseList($result['accounts']->getItems(), AccountResource::class),
            'allAccounts' => new AccountsSelectResource($result['allAccounts']->getItems(), false),
            'total'       => $result['accounts']->getTotal(),
            'historyUrl'  => HistoryChangesRoute::make(type: HistoryType::ACCOUNT),
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function save(DefaultRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::ACCOUNTS_EDIT)) {
            abort(403);
        }

        $account = $this->saveCommand->execute(
            $request->getIntOrNull('id'),
            $request->getStringOrNull('number'),
            $request->getBool('is_invoicing'),
            $request->getIntOrNull('size'),
            $request->getStringOrNull('cadastreNumber'),
        );

        if ($account === null) {
            abort(404);
        }

        return response()->json([
            'account' => new AccountResource($account),
        ]);
    }

    public function delete(int $id): bool
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::ACCOUNTS_DROP)) {
            abort(403);
        }

        return $this->accountService->deleteById($id);
    }

    /**
     * @param int $id
     *
     * @return \Core\Domains\Account\AccountEntity|null
     */
    public function getAccount(int $id): ?\Core\Domains\Account\AccountEntity
    {
        $roleDecorator = lc::roleDecorator();

        if ( ! $roleDecorator->can(PermissionEnum::ACCOUNTS_VIEW)) {
            abort(403);
        }
        if ( ! $id && ! $roleDecorator->can(PermissionEnum::ACCOUNTS_EDIT)) {
            abort(403);
        }
        $accountSearcher = new AccountSearcher();
        $accountSearcher
            ->setId($id)
            ->setWithUsers()
        ;
        $account = $this->accountService->search($accountSearcher)->getItems()->first();

        return $account;
    }
}
