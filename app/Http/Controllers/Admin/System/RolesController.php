<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\Roles\RoleResource;
use App\Http\Resources\Admin\Roles\RolesListResource;
use App\Resources\RouteNames;
use Core\App\Access\SaveRoleCommand;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Access\RoleFactory;
use Core\Domains\Access\RoleSearcher;
use Core\Domains\Access\RoleService;
use Core\Domains\HistoryChanges\HistoryType;
use Core\Exceptions\ValidationException;
use Illuminate\Http\JsonResponse;
use lc;

class RolesController extends Controller
{

    public function __construct(
        private readonly RoleFactory     $roleFactory,
        private readonly RoleService     $roleService,
        private readonly SaveRoleCommand $saveRoleCommand,
    )
    {
    }

    public function index()
    {
        if (lc::roleDecorator()->can(PermissionEnum::ROLES_VIEW)) {
            return view('admin.pages.roles');
        }

        abort(403);
    }

    public function create(): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::ROLES_EDIT)) {
            abort(403);
        }

        return response()->json(new RoleResource($this->roleFactory->makeDefault()));
    }

    public function list(): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::ROLES_VIEW)) {
            abort(403);
        }

        $roles = $this->roleService->search((new RoleSearcher())
            ->setWithUsers(),
        );

        return response()->json([
            'roles'      => new RolesListResource($roles->getItems()),
            'historyUrl' => route(RouteNames::HISTORY_CHANGES, ['type' => HistoryType::ROLE->value]),
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function save(DefaultRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::ROLES_EDIT)) {
            abort(403);
        }

        $role = $this->saveRoleCommand->execute(
            $request->getIntOrNull('id'),
            $request->getStringOrNull('name'),
            $request->getArray('permissions'),
        );

        if ($role === null) {
            abort(404);
        }

        return response()->json(new RoleResource($role));
    }

    public function delete(int $id): bool
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::ROLES_DROP)) {
            abort(403);
        }

        return $this->roleService->deleteById($id);
    }
}
