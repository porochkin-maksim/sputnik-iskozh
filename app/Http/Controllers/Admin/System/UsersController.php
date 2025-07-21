<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\System;

use App\Exports\UsersExport\UsersExport;
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
use Carbon\Carbon;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Access\RoleLocator;
use Core\Domains\Access\Services\RoleService;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Collections\AccountCollection;
use Core\Domains\Account\Models\AccountSearcher;
use Core\Domains\Account\Services\AccountService;
use Core\Domains\Infra\ExData\Enums\ExDataTypeEnum;
use Core\Domains\Infra\ExData\ExDataLocator;
use Core\Domains\Infra\ExData\Services\ExDataService;
use Core\Domains\User\Factories\UserFactory;
use Core\Domains\User\Models\UserSearcher;
use Core\Domains\User\Services\UserService;
use Core\Domains\User\UserLocator;
use Core\Requests\RequestArgumentsEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use lc;
use Maatwebsite\Excel\Facades\Excel;

class UsersController extends Controller
{
    private UserFactory    $userFactory;
    private UserService    $userService;
    private AccountService $accountService;
    private RoleService    $roleService;
    private ExDataService  $exDataService;

    public function __construct()
    {
        $this->userFactory    = UserLocator::UserFactory();
        $this->userService    = UserLocator::UserService();
        $this->accountService = AccountLocator::AccountService();
        $this->roleService    = RoleLocator::RoleService();
        $this->exDataService  = ExDataLocator::ExDataService();
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
            abort(412);
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
            ->setSortOrderProperty(Account::SORT_VALUE, SearcherInterface::SORT_ORDER_ASC)
        ;
        $accountsCollection = $this->accountService->search($accountSearcher);

        $accounts = new AccountsSelectResource($accountsCollection->getItems(), false);

        return view('admin.pages.users.view', compact('user', 'accounts', 'roles'));
    }

    public function list(ListRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::USERS_VIEW)) {
            abort(403);
        }

        $searcher = new UserSearcher();
        $searcher
            ->setWithAccounts()
            ->setLimit($request->getLimit())
            ->setOffset($request->getOffset())
        ;

        $searchString = $request->getStringOrNull(RequestArgumentsEnum::SEARCH);
        if ($searchString) {
            $searcher->addOrWhere(User::LAST_NAME, SearcherInterface::LIKE, "{$searchString}%")
                ->addOrWhere(User::FIRST_NAME, SearcherInterface::LIKE, "{$searchString}%")
                ->addOrWhere(User::EMAIL, SearcherInterface::LIKE, "{$searchString}%")
                ->addOrWhere(User::PHONE, SearcherInterface::LIKE, "{$searchString}%")
            ;
        }

        if ($request->getSortField() && $request->getSortOrder()) {
            $searcher->setSortOrderProperty(
                $request->getSortField(),
                $request->getSortOrder() === 'asc' ? SearcherInterface::SORT_ORDER_ASC : SearcherInterface::SORT_ORDER_DESC,
            );
        }
        else {
            $searcher->setSortOrderProperty(User::ID, SearcherInterface::SORT_ORDER_DESC);
        }

        $users = $this->userService->search($searcher);

        return response()->json(new UsersListResource(
            $users->getItems(),
            $users->getTotal(),
        ));
    }

    public function export(ListRequest $request)
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::USERS_VIEW)) {
            abort(403);
        }

        $searcher = new UserSearcher();
        $searcher
            ->setWithAccounts()
            ->setSortOrderProperty('account_sort', SearcherInterface::SORT_ORDER_ASC)
        ;

        $users = $this->userService->search($searcher)->getItems();

        return Excel::download(new UsersExport($users), sprintf('Пользователи-%s.xlsx', now()->format('Y-m-d-hi')));
    }

    public function save(SaveRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::USERS_EDIT)) {
            abort(403);
        }

        $user = $request->getId()
            ? $this->userService->getById($request->getId())
            : $this->userFactory->makeDefault()
                ->setPassword(Str::random(8))
        ;

        if ( ! $user) {
            abort(412);
        }

        $user->setFirstName($request->getFirstName())
            ->setMiddleName($request->getMiddleName())
            ->setLastName($request->getLastName())
            ->setEmail($request->getEmail())
            ->setPhone($request->getPhone())
            ->setRole($this->roleService->getById($request->getRoleId()))
            ->setOwnershipDutyInfo($request->getOwnershipDutyInfo())
            ->setOwnershipDate($request->getOwnershipDate())
        ;

        $accounts = $request->getAccountIds() ? $this->accountService->getByIds($request->getAccountIds()) : new AccountCollection();
        $user->setAccounts($accounts);

        $user = $this->userService->save($user);

        $exData = $this->exDataService->getByTypeAndReferenceId(ExDataTypeEnum::USER, $user->getId())
            ? : $this->exDataService->makeDefault(ExDataTypeEnum::USER)->setReferenceId($user->getId());

        $exData->setData($user->getExData()
            ->setPhone($request->getAddPhone())
            ->setLegalAddress($request->getLegalAddress())
            ->setPostAddress($request->getPostAddress())
            ->setAdditional($request->getAdditional())
            ->jsonSerialize(),
        );

        $this->exDataService->save($exData);

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
            abort(412);
        }

        UserLocator::Notificator()->sendRestorePassword($user);

        if ( ! $user->getEmailVerifiedAt()) {
            $this->userService->save($user->setEmailVerifiedAt(Carbon::now()));
        }
    }

    public function sendInviteWithPassword(DefaultRequest $request): void
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::USERS_EDIT)) {
            abort(403);
        }

        $user = $this->userService->getById($request->getInt('id'));

        if ( ! $user || ! $user->getModel()) {
            abort(412);
        }

        UserLocator::Notificator()->sendInviteNotification($user);

        if ( ! $user->getEmailVerifiedAt()) {
            $this->userService->save($user->setEmailVerifiedAt(Carbon::now()));
        }
    }

    public function generateEmail(DefaultRequest $request): ?string
    {
        $lastName   = $request->getStringOrNull(RequestArgumentsEnum::LAST_NAME);
        $firstName  = $request->getStringOrNull(RequestArgumentsEnum::FIRST_NAME);
        $middleName = $request->getStringOrNull(RequestArgumentsEnum::MIDDLE_NAME);

        return Str::slug($lastName) . '.' . Str::slug($firstName) . '@' . Str::slug($middleName) . '.ru';
    }
}
