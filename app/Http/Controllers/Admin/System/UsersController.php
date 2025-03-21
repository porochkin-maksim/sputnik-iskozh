<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Users\ListRequest;
use App\Http\Requests\Admin\Users\SaveRequest;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\Accounts\AccountsSelectResource;
use App\Http\Resources\Admin\Roles\RolesSelectResource;
use App\Http\Resources\Admin\Users\UserResource;
use App\Http\Resources\Admin\Users\UsersListResource;
use App\Models\Account\Account;
use App\Models\User;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Access\RoleLocator;
use Core\Domains\Access\Services\RoleService;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Models\AccountSearcher;
use Core\Domains\Account\Services\AccountService;
use Core\Domains\User\Factories\UserFactory;
use Core\Domains\User\Models\UserSearcher;
use Core\Domains\User\Services\UserService;
use Core\Domains\User\UserLocator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use lc;

class UsersController extends Controller
{
    private UserFactory    $userFactory;
    private UserService    $userService;
    private AccountService $accountService;
    private RoleService    $roleService;

    public function __construct()
    {
        $this->userFactory    = UserLocator::UserFactory();
        $this->userService    = UserLocator::UserService();
        $this->accountService = AccountLocator::AccountService();
        $this->roleService    = RoleLocator::RoleService();
    }

    public function view(?int $id = null)
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::USERS_VIEW)) {
            abort(403);
        }
        if ( ! $id && ! lc::roleDecorator()->can(PermissionEnum::USERS_EDIT)) {
            abort(403);
        }
        $user = $id
            ? $this->userService->getById($id)
            : $this->userFactory->makeDefault();
        if ( ! $user) {
            abort(404);
        }

        if ($id) {
            $account = $this->accountService->getByUserId($user->getId());
            $user->setAccount($account);
            $role = $this->roleService->getByUserId($user->getId());
            $user->setRole($role);
        }
        $user = new UserResource($user);

        $roles = new RolesSelectResource($this->roleService->all(), true);

        $accountSearcher = new AccountSearcher();
        $accountSearcher
            ->setWithoutSntAccount()
            ->setSortOrderProperty(Account::NUMBER, SearcherInterface::SORT_ORDER_ASC)
        ;
        $accountsCollection = $this->accountService->search($accountSearcher);

        $accounts = new AccountsSelectResource($accountsCollection->getItems(), true);

        return view('admin.pages.users.view', compact('user', 'accounts', 'roles'));
    }

    public function list(ListRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::USERS_VIEW)) {
            abort(403);
        }

        $searcher = new UserSearcher();
        $searcher
            ->setLimit($request->getLimit())
            ->setOffset($request->getOffset())
            ->setSortOrderProperty(User::ID, SearcherInterface::SORT_ORDER_DESC)
        ;

        $users = $this->userService->search($searcher);

        return response()->json(new UsersListResource(
            $users->getItems(),
            $users->getTotal(),
        ));
    }

    public function save(SaveRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::USERS_EDIT)) {
            abort(403);
        }

        $user = $request->getId()
            ? $this->userService->getById($request->getId())
            : $this->userFactory->makeDefault()->setPassword(Str::random(8));

        if ( ! $user) {
            abort(404);
        }

        $user->setFirstName($request->getFirstName())
            ->setMiddleName($request->getMiddleName())
            ->setLastName($request->getLastName())
            ->setEmail($request->getEmail())
            ->setRole($this->roleService->getById($request->getRoleId()))
            ->setAccount($this->accountService->getById($request->getAccountId()))
        ;

        $user = $this->userService->save($user);

        return response()->json(new UserResource($user));
    }

    public function delete(int $id): bool
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::USERS_DROP)) {
            abort(403);
        }

        return $this->userService->deleteById($id);
    }

    public function sendRestorePassword(DefaultRequest $request): void
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::USERS_EDIT)) {
            abort(403);
        }

        $user = $this->userService->getById($request->getInt('id'));

        if ( ! $user || ! $user->getModel()) {
            abort(404);
        }

        UserLocator::Notificator()->sendRestorePassword($user);
    }
}
