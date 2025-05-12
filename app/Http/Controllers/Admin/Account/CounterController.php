<?php

namespace App\Http\Controllers\Admin\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Counters\AddHistoryRequest;
use App\Http\Requests\Admin\Counters\CreateRequest;
use App\Http\Requests\Admin\Counters\SaveRequest;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\Counters\CounterListResource;
use App\Http\Resources\Admin\Periods\PeriodResource;
use App\Models\Counter\CounterHistory;
use Carbon\Carbon;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Services\AccountService;
use Core\Domains\Billing\Jobs\CheckClaimForCounterChangeJob;
use Core\Domains\Billing\Period\PeriodLocator;
use Core\Domains\Billing\Period\Services\PeriodService;
use Core\Domains\Counter\CounterLocator;
use Core\Domains\Counter\Factories\CounterFactory;
use Core\Domains\Counter\Factories\CounterHistoryFactory;
use Core\Domains\Counter\Models\CounterHistorySearcher;
use Core\Domains\Counter\Services\CounterHistoryService;
use Core\Domains\Counter\Services\CounterService;
use Core\Domains\Counter\Services\FileService;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Domains\Infra\HistoryChanges\Services\HistoryChangesService;
use Core\Enums\DateTimeFormat;
use Core\Requests\RequestArgumentsEnum;
use Core\Responses\ResponsesEnum;
use Exception;
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
    private HistoryChangesService $historyChangesService;
    private PeriodService         $periodService;

    public function __construct()
    {
        $this->accountService        = AccountLocator::AccountService();
        $this->counterService        = CounterLocator::CounterService();
        $this->counterFactory        = CounterLocator::CounterFactory();
        $this->counterHistoryService = CounterLocator::CounterHistoryService();
        $this->counterHistoryFactory = CounterLocator::CounterHistoryFactory();
        $this->fileService           = CounterLocator::FileService();
        $this->historyChangesService = HistoryChangesLocator::HistoryChangesService();
        $this->periodService         = PeriodLocator::PeriodService();
    }

    public function list(int $accountId, DefaultRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::COUNTERS_VIEW)) {
            abort(403);
        }

        $result = $this->counterService->getByAccountId($accountId);

        $period = null;
        $limit  = $request->getLimit() ? : 12;

        $searcher = CounterHistorySearcher::make()
            ->setLimit($limit)
            ->setOffset($request->getOffset())
            ->setWithClaim()
            ->setSortOrderProperty(CounterHistory::DATE, SearcherInterface::SORT_ORDER_DESC)
        ;

        if ($request->getIntOrNull('periodId')) {
            $period = $this->periodService->getById($request->getIntOrNull('periodId'));
        }
        if ($period) {
            $searcher
                ->addWhere(CounterHistory::DATE, SearcherInterface::GTE, $period->getStartAt()?->format(DateTimeFormat::DATE_DEFAULT))
                ->addWhere(CounterHistory::DATE, SearcherInterface::LTE, $period->getEndAt()?->format(DateTimeFormat::DATE_DEFAULT))
            ;
        }
        else {
            $searcher->setLimit($limit);
        }

        foreach ($result as $counter) {
            $counter->setHistoryCollection(
                $this->counterHistoryService->search(
                    $searcher->setCounterId($counter->getId()),
                )->getItems(),
            );
        }

        return response()->json([
            ResponsesEnum::COUNTERS => new CounterListResource($result),
            ResponsesEnum::PERIOD   => $period ? new PeriodResource($period) : null,
        ]);
    }

    public function create(int $accountId, CreateRequest $request): void
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::COUNTERS_EDIT)) {
            abort(403);
        }

        $account = $this->accountService->getById($accountId);

        if ( ! $account) {
            abort(404);
        }

        DB::beginTransaction();
        try {
            $counter = $this->counterFactory->makeDefault()
                ->setIsInvoicing($request->getIsInvoicing())
                ->setNumber($request->getNumber())
                ->setAccountId($account->getId())
            ;

            $counter = $this->counterService->save($counter);

            $history = $this->counterHistoryFactory->makeDefault()
                ->setIsVerified(true)
                ->setCounterId($counter->getId())
                ->setValue($request->getValue())
            ;
            $history = $this->counterHistoryService->save($history);

            if ($request->getFile()) {
                $this->fileService->store($request->getFile(), $history->getId());
            }
            DB::commit();
        }
        catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function save(int $accountId, SaveRequest $request): void
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::COUNTERS_EDIT)) {
            abort(403);
        }

        $counter = $this->counterService->getById($request->getId());

        if ( ! $counter || $counter->getAccountId() !== $accountId) {
            abort(404);
        }

        $counter
            ->setIsInvoicing($request->getIsInvoicing())
            ->setIncrement($request->getIncrement())
            ->setNumber($request->getNumber())
        ;

        $this->counterService->save($counter);
    }

    public function addValue(int $accountId, AddHistoryRequest $request): void
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::COUNTERS_EDIT)) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            $counter = $this->counterService->getById($request->getCounterId());

            if ( ! $counter || $counter->getAccountId() !== $accountId) {
                abort(404);
            }

            $lastHistory = $this->counterHistoryService->getLastByCounterId($counter->getId());
            $history     = $this->counterHistoryService->getById($request->getId())
                ? : $this->counterHistoryFactory->makeDefault()
                    ->setPreviousId($lastHistory?->getId())
                    ->setCounterId($counter->getId())
            ;

            $history = $history
                ->setDate($request->getDate())
                ->setIsVerified(true)
                ->setValue($request->getValue())
            ;

            $history = $this->counterHistoryService->save($history);

            if ($request->getId() && $request->getFile() !== null) {
                $file = $this->fileService->getByHistoryId($history->getId());
                $this->fileService->deleteById($file?->getId());
            }

            if ($request->getFile()) {
                $this->fileService->store($request->getFile(), $history->getId());
            }

            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function createClaim(int $counterHistoryId): bool
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::COUNTERS_EDIT)) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            $history = $this->counterHistoryService->getById($counterHistoryId);
            if ($history && ! $history->isVerified()) {
                $history->setIsVerified(true);
                $this->counterHistoryService->save($history);
            }

            dispatch_sync(new CheckClaimForCounterChangeJob($counterHistoryId));
            DB::commit();

            return true;
        }
        catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(int $accountId, int $counterId, DefaultRequest $request): bool
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::COUNTERS_DROP)) {
            abort(403);
        }

        $comment = $request->getStringOrNull(RequestArgumentsEnum::COMMENT);

        DB::beginTransaction();
        try {
            $result = $this->counterService->deleteById($counterId);
            if ($result && $comment) {
                $this->historyChangesService->writeToHistory(
                    Event::COMMON,
                    HistoryType::COUNTER,
                    $counterId,
                    text: sprintf('Удалён по причине: %s', $comment),
                );
            }
            DB::commit();

            return $result;
        }
        catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}