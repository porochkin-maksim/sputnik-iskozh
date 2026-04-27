<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Roles\SaveRequest;
use App\Http\Resources\Admin\Roles\RoleResource;
use App\Http\Resources\Admin\Roles\RolesListResource;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Access\Factories\RoleFactory;
use Core\Domains\Access\Models\RoleSearcher;
use Core\Domains\Access\RoleLocator;
use Core\Domains\Access\Services\RoleService;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Resources\Views\ViewNames;
use Illuminate\Http\JsonResponse;
use lc;

class RolesController extends Controller
{
    private RoleFactory $roleFactory;
    private RoleService $roleService;

    public function __construct()
    {
        $this->roleFactory = RoleLocator::RoleFactory();
        $this->roleService = RoleLocator::RoleService();
    }

    public function index()
    {
        if (lc::roleDecorator()->can(PermissionEnum::ROLES_VIEW)) {
            return view(ViewNames::ADMIN_PAGES_ROLES);
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

        $roles = $this->roleService->search(new RoleSearcher()
            ->setWithUsers()
        );

        return response()->json([
            'roles'      => new RolesListResource($roles->getItems()),
            'historyUrl' => HistoryChangesLocator::route(type: HistoryType::ROLE),
        ]);
    }

    public function save(SaveRequest $request): JsonResponse
    {
        if ($request->getId() && ! lc::roleDecorator()->can(PermissionEnum::ROLES_EDIT)) {
            abort(403);
        }

        if ( ! $request->getId() && ! lc::roleDecorator()->can(PermissionEnum::ROLES_EDIT)) {
            abort(403);
        }

        $role = $request->getId()
            ? $this->roleService->getById($request->getId())
            : $this->roleFactory->makeDefault();

        if ( ! $role) {
            abort(404);
        }

        $role
            ->setName($request->getName())
            ->setPermissions($request->getPersmissions())
        ;

        $role = $this->roleService->save($role);

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
