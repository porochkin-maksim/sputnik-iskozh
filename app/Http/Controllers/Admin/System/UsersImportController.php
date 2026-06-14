<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use Core\App\User\Import\UsersImportService;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use Core\Domains\Infra\DbLock\Service\LockService;
use Core\Domains\User\Events\ImportUsersSaveRequested;
use Illuminate\Http\JsonResponse;
use lc;

class UsersImportController extends Controller
{
    public function __construct(
        private readonly UsersImportService $usersImportService,
        private readonly LockService        $lockService,
    )
    {
    }

    public function index()
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::USERS_VIEW)) {
            abort(403);
        }

        $locked = $this->lockService->isLocked(LockNameEnum::SAVE_IMPORT_USERS_JOB);

        return view('pages.admin.users.import', compact('locked'));
    }

    public function parseFile(DefaultRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::USERS_EDIT)) {
            abort(403);
        }

        $file = $request->file('file');
        if ( ! $file) {
            return response()->json(['error' => 'Файл не загружен'], 422);
        }

        $path          = $file->getPath();
        $items         = $this->usersImportService->parseFile($path);
        $changedItems  = [];

        foreach ($items as $item) {
            if ($item->hasChanges()) {
                $changedItems[] = $item;
            }
        }

        return response()->json([
            'total'   => count($items),
            'changes' => count($changedItems),
            'items'   => $changedItems,
        ]);
    }

    public function save(DefaultRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::USERS_EDIT)) {
            abort(403);
        }

        if ( ! $this->lockService->isAvailable(LockNameEnum::SAVE_IMPORT_USERS_JOB)) {
            return response()->json(['error' => 'Система осуществляет импорт предыдущих данных'], 423);
        }

        $users = $request->getArray('users');

        event(new ImportUsersSaveRequested(
            users   : $users,
            lockName: LockNameEnum::SAVE_IMPORT_USERS_JOB,
        ));

        return response()->json(['success' => true]);
    }
}
