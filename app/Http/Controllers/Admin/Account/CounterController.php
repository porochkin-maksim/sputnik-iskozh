<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\Counters\CounterListResource;
use Carbon\Carbon;
use Core\App\Counter\CreateAdminCounterCommand;
use Core\App\Counter\DeleteAdminCounterCommand;
use Core\App\Counter\SaveAdminCounterCommand;
use Core\App\CounterHistory\AddAdminCounterHistoryCommand;
use Core\App\CounterHistory\CreateCounterClaimCommand;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Account\AccountService;
use Core\Domains\Counter\CounterService;
use Core\Domains\CounterHistory\CounterHistoryCollection;
use Core\Domains\CounterHistory\CounterHistoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use lc;
use Throwable;

class CounterController extends Controller
{
    public function __construct(
        private readonly AccountService                $accountService,
        private readonly CounterService                $counterService,
        private readonly CounterHistoryService         $counterHistoryService,
        private readonly CreateAdminCounterCommand     $createAdminCounterCommand,
        private readonly SaveAdminCounterCommand       $saveAdminCounterCommand,
        private readonly AddAdminCounterHistoryCommand $addAdminCounterHistoryCommand,
        private readonly CreateCounterClaimCommand     $createCounterClaimCommand,
        private readonly DeleteAdminCounterCommand     $deleteAdminCounterCommand,
    )
    {
    }

    public function index()
    {
        if (lc::roleDecorator()->can(PermissionEnum::COUNTERS_VIEW)) {
            return view('admin.pages.counter-history');
        }

        abort(403);
    }

    public function list(int $accountId): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::COUNTERS_VIEW)) {
            abort(403);
        }

        $result = $this->counterService->getByAccountId($accountId);

        foreach ($result as $counter) {
            $lastHistory = $this->counterHistoryService->getLastByCounterId($counter->getId());
            if ($lastHistory) {
                $counter->setHistoryCollection(new CounterHistoryCollection([$lastHistory]));
            }
        }

        return response()->json([
            'counters' => new CounterListResource($result),
        ]);
    }

    public function create(int $accountId, DefaultRequest $request): void
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::COUNTERS_EDIT)) {
            abort(403);
        }

        $this->createAdminCounterCommand->execute(
            $accountId,
            $request->getBool('isInvoicing'),
            $request->getString('number'),
            $request->getInt('value'),
            $request->file('file'),
            $request->file('passportFile'),
        );
    }

    public function view(int $accountId, int $counterId): View
    {
        $counter = $this->counterService->getById($counterId);

        if ( ! $counter || $counter->getAccountId() !== $accountId) {
            abort(404);
        }

        $account = $this->accountService->getById($accountId);
        $counter->setAccount($account);

        $history = $this->counterHistoryService->getLastByCounterId($counterId);
        if ($history) {
            $counter->setHistoryCollection(new CounterHistoryCollection([$history]));
        }

        return view('admin.pages.accounts.counters.view', compact('counter'));
    }

    public function save(int $accountId, DefaultRequest $request): void
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::COUNTERS_EDIT)) {
            abort(403);
        }

        $this->saveAdminCounterCommand->execute(
            $accountId,
            $request->getInt('id'),
            $request->getBool('isInvoicing'),
            abs($request->getInt('increment')),
            $request->getString('number'),
            $request->getDateOrNull('expireAt'),
            $request->file('passportFile'),
        );
    }

    public function addValue(int $accountId, DefaultRequest $request): void
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::COUNTERS_EDIT)) {
            abort(403);
        }

        $this->addAdminCounterHistoryCommand->execute(
            $accountId,
            $request->getIntOrNull('id'),
            $request->getInt('counter_id'),
            $request->getDateOrNull('date') ? : Carbon::now(),
            $request->getInt('value'),
            $request->file('file'),
        );
    }

    /**
     * @throws Throwable
     */
    public function createClaim(int $counterHistoryId): bool
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::COUNTERS_EDIT)) {
            abort(403);
        }

        return $this->createCounterClaimCommand->execute($counterHistoryId);
    }

    public function delete(int $accountId, int $counterId, DefaultRequest $request): bool
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::COUNTERS_DROP)) {
            abort(403);
        }

        return $this->deleteAdminCounterCommand->execute($counterId, $request->getStringOrNull('comment'));
    }
}
