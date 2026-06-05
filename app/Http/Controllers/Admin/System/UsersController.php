<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\System;

use App\Exports\UsersExport\UsersExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\Users\UserResource;
use App\Http\Resources\Admin\Users\UsersListResource;
use App\Support\HistoryChangesRoute;
use App\Models\User;
use App\Services\Users\Notificator;
use Carbon\Carbon;
use Core\App\User\GetListCommand;
use Core\App\User\SaveCommand;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Access\RoleService;
use Core\Domains\Account\AccountCollection;
use Core\Domains\Account\AccountService;
use Core\Domains\HistoryChanges\HistoryType;
use Core\Domains\User\UserEntity;
use Core\Domains\User\UserFactory;
use Core\Domains\User\UserService;
use Core\Exceptions\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use lc;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Exception;

class UsersController extends Controller
{

    public function __construct(
        private readonly UserFactory    $userFactory,
        private readonly UserService    $userService,
        private readonly AccountService $accountService,
        private readonly RoleService    $roleService,
        private readonly Notificator    $notificator,
        private readonly GetListCommand $getListCommand,
        private readonly SaveCommand    $saveCommand,
    )
    {
    }

    // blade: resources/views/admin/pages/users.blade.php
    // vue: resources/js/components/admin/users/UsersBlock.vue
    public function index()
    {
        if (lc::roleDecorator()->can(PermissionEnum::USERS_VIEW)) {
            return view('pages.admin.users.index');
        }

        abort(403);
    }

    // blade: resources/views/admin/pages/users/view.blade.php
    // vue: resources/js/components/admin/users/UserItemView.vue
    public function view(?int $id = null)
    {
        $this->assertUserViewAccess($id);

        $user = $this->makeUserForView($id);

        return view('pages.admin.users.view', compact('user'));
    }


    // vue: resources/js/components/admin/users/UserItemView.vue
    public function get(?int $id = null): JsonResponse
    {
        $this->assertUserViewAccess($id);

        $user = $this->makeUserForView($id);

        if ( ! $user) {
            abort(412);
        }

        return response()->json(new UserResource($user));
    }

    /**
     * @throws ValidationException
     */
    // vue: resources/js/components/admin/users/UsersBlock.vue
    public function list(DefaultRequest $request): JsonResponse
    {
        $roleDecorator = lc::roleDecorator();

        if ( ! $roleDecorator->can(PermissionEnum::USERS_VIEW)) {
            abort(403);
        }

        $users = $this->getListCommand->execute(
            $request->getLimit(),
            $request->getOffset(),
            $request->getSortField(),
            $request->getSortOrder(),
            $request->getStringOrNull('search'),
            $request->getBool('isDeleted'),
            $request->input('isMember'),
            $request->getBool('isMember'),
        );

        return response()->json([
            'users'      => new UsersListResource($users->getItems()),
            'total'      => $users->getTotal(),
            'historyUrl' => HistoryChangesRoute::make(type: HistoryType::USER),
        ]);
    }

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws ValidationException
     */
    public function export(DefaultRequest $request)
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::USERS_VIEW)) {
            abort(403);
        }

        $requestData = $request->toArray();
        unset($requestData['skip'], $requestData['limit']);

        $newRequest = new DefaultRequest($requestData);
        $users      = $this->getListCommand->execute(
            $newRequest->getLimit(),
            $newRequest->getOffset(),
            $newRequest->getSortField(),
            $newRequest->getSortOrder(),
            $newRequest->getStringOrNull('search'),
            $newRequest->getBool('isDeleted'),
            $newRequest->input('isMember'),
            $newRequest->getBool('isMember'),
        )->getItems();

        return Excel::download(new UsersExport($users), sprintf('Пользователи-%s.xlsx', now()->format('Y-m-d-hi')));
    }

    public function save(DefaultRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::USERS_EDIT)) {
            abort(403);
        }

        $user = $this->saveCommand->execute(
            $request->getIntOrNull('id'),
            $request->getStringOrNull('first_name'),
            $request->getStringOrNull('middle_name'),
            $request->getStringOrNull('last_name'),
            $request->getStringOrNull('email'),
            $request->getStringOrNull('phone'),
            $request->getInt('role_id'),
            $request->getStringOrNull('membershipDutyInfo'),
            $request->getDateOrNull('membershipDate'),
            $request->getArray('fractions'),
            $request->getArray('ownerDates'),
            $request->getStringOrNull('add_phone'),
            $request->getStringOrNull('legal_address'),
            $request->getStringOrNull('post_address'),
            $request->getStringOrNull('additional'),
        );

        if ($user === null) {
            abort(412);
        }

        return response()->json(new UserResource($user));
    }

    public function delete(int $id): bool
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::USERS_DROP)) {
            abort(403);
        }

        return $this->userService->deleteById($id);
    }

    public function restore(int $id): bool
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::USERS_DROP)) {
            abort(403);
        }

        $user = User::withTrashed()->find($id);
        $user?->restore();

        return (bool) $user?->id;
    }

    public function sendRestorePassword(DefaultRequest $request): void
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::USERS_EDIT)) {
            abort(403);
        }

        $user = $this->userService->getById($request->getInt('id'));

        if ( ! $user?->getId()) {
            abort(412);
        }

        $this->notificator->sendRestorePassword($user);
    }

    public function sendInviteWithPassword(DefaultRequest $request): void
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::USERS_EDIT)) {
            abort(403);
        }

        $user = $this->userService->getById($request->getInt('id'));

        if ( ! $user?->getId()) {
            abort(412);
        }

        $this->notificator->sendInviteNotification($user);

        if ( ! $user->getEmailVerifiedAt()) {
            $this->userService->save($user->setEmailVerifiedAt(Carbon::now()));
        }
    }

    public function generateEmail(DefaultRequest $request): ?string
    {
        $lastName   = $request->getStringOrNull('last_name');
        $firstName  = $request->getStringOrNull('first_name');
        $middleName = $request->getStringOrNull('middle_name');

        return Str::slug($lastName) . '.' . Str::slug($firstName) . '@' . Str::slug($middleName) . '.ru';
    }

    private function assertUserViewAccess(?int $id): void
    {
        $roleDecorator = lc::roleDecorator();

        if ( ! $roleDecorator->can(PermissionEnum::USERS_VIEW)) {
            abort(403);
        }
        if ( ! $id && ! $roleDecorator->can(PermissionEnum::USERS_EDIT)) {
            abort(403);
        }
    }

    private function makeUserForView(?int $id = null): ?UserEntity
    {
        $user = $id
            ? $this->userService->getById($id)
            : $this->userFactory->makeDefault();

        if ( ! $user) {
            return null;
        }

        if ($id) {
            $account = $this->accountService->getByUserId($user->getId())->first();
            $user->setAccount($account);
            $role = $this->roleService->getByUserId($user->getId());
            $user->setRole($role);
        }
        else {
            $accountId = DefaultRequest::make()->getIntOrNull('accountId');
            if ($accountId) {
                $account = $this->accountService->getById($accountId)
                    ?->setFraction(1)
                    ?->setOwnerDate(Carbon::now())
                ;

                $user->setAccount($account);
                $user->setAccounts(new AccountCollection([$account]));
            }
        }

        return $user;
    }
}
