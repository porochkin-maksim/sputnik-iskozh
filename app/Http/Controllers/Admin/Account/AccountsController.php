<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\Accounts\AccountResource;
use App\Http\Resources\Admin\Accounts\AccountsListResource;
use Core\App\Account\GetListCommand;
use Core\App\Account\SaveCommand;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Account\AccountFactory;
use Core\Domains\Account\AccountSearcher;
use Core\Domains\Account\AccountService;
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

    public function index()
    {
        if (lc::roleDecorator()->can(PermissionEnum::ACCOUNTS_VIEW)) {
            return view('admin.pages.accounts');
        }

        abort(403);
    }

    public function create(): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::ACCOUNTS_EDIT)) {
            abort(403);
        }

        return response()->json(new AccountResource($this->accountFactory->makeDefault()));
    }

    public function view($id)
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

        foreach ($account->getUsers() as $user) {
            $user->setAccount($account);
        }

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

    public function list(DefaultRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::ACCOUNTS_VIEW)) {
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

        return response()->json(new AccountsListResource(
            $result['accounts']->getItems(),
            $result['accounts']->getTotal(),
            $result['allAccounts']->getItems(),
        ));
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
}
