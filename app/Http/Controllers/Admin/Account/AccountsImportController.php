<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use Core\App\Account\Import\AccountsImportService;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Account\Events\ImportAccountsSaveRequested;
use Core\Domains\Infra\DbLock\Enum\LockNameEnum;
use Core\Domains\Infra\DbLock\Service\LockService;
use Illuminate\Http\JsonResponse;
use lc;

class AccountsImportController extends Controller
{
    public function __construct(
        private readonly AccountsImportService $accountsImportService,
        private readonly LockService           $lockService,
    )
    {
    }

    public function index()
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::ACCOUNTS_VIEW)) {
            abort(403);
        }

        $locked = $this->lockService->isLocked(LockNameEnum::SAVE_IMPORT_ACCOUNTS_JOB);

        return view('pages.admin.accounts.import', compact('locked'));
    }

    public function parseFile(DefaultRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::ACCOUNTS_EDIT)) {
            abort(403);
        }

        $file = $request->file('file');
        if ( ! $file) {
            return response()->json(['error' => 'Файл не загружен'], 422);
        }

        $path          = $file->getPath();
        $items         = $this->accountsImportService->parseFile($path);
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
        if ( ! lc::roleDecorator()->can(PermissionEnum::ACCOUNTS_EDIT)) {
            abort(403);
        }

        if ( ! $this->lockService->isAvailable(LockNameEnum::SAVE_IMPORT_ACCOUNTS_JOB)) {
            return response()->json(['error' => 'Система осуществляет импорт предыдущих данных'], 423);
        }

        $accounts = $request->getArray('accounts');

        event(new ImportAccountsSaveRequested(
            accounts: $accounts,
            lockName: LockNameEnum::SAVE_IMPORT_ACCOUNTS_JOB,
        ));

        return response()->json(['success' => true]);
    }
}
