<?php

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Counters\AddHistoryRequest;
use App\Http\Requests\Admin\Counters\CreateRequest;
use App\Http\Requests\Admin\Counters\SaveRequest;
use App\Http\Resources\Profile\Counters\CounterListResource;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Services\AccountService;
use Core\Domains\Billing\Jobs\CreateTransactionForCounterChangeJob;
use Core\Domains\Counter\CounterLocator;
use Core\Domains\Counter\Factories\CounterFactory;
use Core\Domains\Counter\Factories\CounterHistoryFactory;
use Core\Domains\Counter\Services\CounterHistoryService;
use Core\Domains\Counter\Services\CounterService;
use Core\Domains\Counter\Services\FileService;
use Core\Responses\ResponsesEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use lc;

class CounterController extends Controller
{
    private AccountService        $accountService;
    private CounterService        $counterService;
    private CounterFactory        $counterFactory;
    private CounterHistoryService $counterHistoryService;
    private CounterHistoryFactory $counterHistoryFactory;
    private FileService           $fileService;

    public function __construct()
    {
        $this->accountService        = AccountLocator::AccountService();
        $this->counterService        = CounterLocator::CounterService();
        $this->counterFactory        = CounterLocator::CounterFactory();
        $this->counterHistoryService = CounterLocator::CounterHistoryService();
        $this->counterHistoryFactory = CounterLocator::CounterHistoryFactory();
        $this->fileService           = CounterLocator::FileService();
    }

    public function list(int $accountId): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::COUNTERS_VIEW)) {
            abort(403);
        }

        $result = $this->counterService->getByAccountId($accountId);

        return response()->json([
            ResponsesEnum::COUNTERS => new CounterListResource($result),
        ]);
    }

    public function create(int $accountId, CreateRequest $request): void
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::COUNTERS_EDIT)) {
            abort(403);
        }

        $account = $this->accountService->getById($accountId);

        if ($account) {
            DB::beginTransaction();
            $counter = $this->counterFactory->makeDefault()
                ->setIsInvoicing($request->getIsInvoicing())
                ->setNumber($request->getNumber())
                ->setAccountId($account->getId())
            ;

            $counter = $this->counterService->save($counter);

            $history = $this->counterHistoryFactory->makeDefault()
                ->setCounterId($counter->getId())
                ->setValue($request->getValue())
            ;
            $history = $this->counterHistoryService->save($history);

            if ($request->getFile()) {
                $this->fileService->store($request->getFile(), $history->getId());
            }
            DB::commit();
        }
    }

    public function save(int $accountId, SaveRequest $request): void
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::COUNTERS_EDIT)) {
            abort(403);
        }

        if ($this->accountService->getById($accountId)) {
            DB::beginTransaction();
            $counter = $this->counterService->getById($request->getId())
                ->setIsInvoicing($request->getIsInvoicing())
                ->setNumber($request->getNumber())
            ;

            $this->counterService->save($counter);
            DB::commit();
        }
    }

    public function addValue(int $accountId, AddHistoryRequest $request): void
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::COUNTERS_EDIT)) {
            abort(403);
        }

        DB::beginTransaction();
        $counter = $this->counterService->getById($request->getCounterId());

        if ( ! $counter) {
            abort(404);
        }

        $history = $this->counterHistoryFactory->makeDefault()
            ->setIsVerified(true)
            ->setCounterId($counter->getId())
            ->setValue($request->getValue())
        ;

        $history = $this->counterHistoryService->save($history);

        CreateTransactionForCounterChangeJob::dispatch($history->getId());

        if ($request->getFile()) {
            $this->fileService->store($request->getFile(), $history->getId());
        }
        DB::commit();
    }
}