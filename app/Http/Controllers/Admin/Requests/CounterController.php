<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\Requests;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Counters\ConfirmRequest;
use App\Http\Requests\Admin\Counters\LinkRequest;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\Counters\CounterHistoryListResource;
use App\Models\Account\Account;
use App\Models\Counter\Counter;
use App\Models\Counter\CounterHistory;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Models\AccountSearcher;
use Core\Domains\Account\Services\AccountService;
use Core\Domains\Billing\Jobs\CheckClaimForCounterChangeJob;
use Core\Domains\Counter\CounterLocator;
use Core\Domains\Counter\Models\CounterHistorySearcher;
use Core\Domains\Counter\Models\CounterSearcher;
use Core\Domains\Counter\Services\CounterHistoryService;
use Core\Domains\Counter\Services\CounterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use lc;
use Throwable;

class CounterController extends Controller
{
    private CounterHistoryService $counterHistoryService;
    private CounterService        $counterService;
    private AccountService        $accountService;

    public function __construct()
    {
        $this->counterHistoryService = CounterLocator::CounterHistoryService();
        $this->counterService        = CounterLocator::CounterService();
        $this->accountService        = AccountLocator::AccountService();
    }

    public function list(DefaultRequest $request): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::COUNTERS_VIEW)) {
            abort(403);
        }

        $searcher = CounterHistorySearcher::make()
            ->setWithCounter()
            ->setWithPrevious()
            ->setVerified($request->getBool('verified'))
        ;

        if ($request->getBool('verified')) {
            $searcher
                ->setLimit($request->getLimit())
                ->setOffset($request->getOffset())
                ->setSortOrderProperty(CounterHistory::DATE, SearcherInterface::SORT_ORDER_DESC)
                ->setSortOrderProperty(CounterHistory::CREATED_AT, SearcherInterface::SORT_ORDER_DESC)
            ;
            if ($request->getSearch()) {
                $accountIds = $this->accountService->search(
                    AccountSearcher::make()->addWhere(Account::NUMBER, SearcherInterface::LIKE, "%{$request->getSearch()}%"),
                )->getItems()->getIds();

                $counterIds = $this->counterService->search(
                    CounterSearcher::make()->addWhere(Counter::ACCOUNT_ID, SearcherInterface::IN, $accountIds),
                )->getItems()->getIds();

                $searcher->setCounterIds($counterIds);
            }
        }
        else {
            $searcher->defaultSort();
        }

        $response         = $this->counterHistoryService->search($searcher);
        $counterHistories = $response->getItems();
        if ($counterHistories->hasUnlinked()) {
            $counterHistories = $counterHistories->getUnlinked();
        }

        return response()->json([
            'histories' => new CounterHistoryListResource($counterHistories),
            'total'     => $response->getTotal(),
        ]);
    }

    public function link(LinkRequest $request): void
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::COUNTERS_EDIT)) {
            abort(403);
        }

        $history = $this->counterHistoryService->getById($request->getId());

        if ( ! $history) {
            abort(404);
        }

        $searcher = new CounterHistorySearcher();
        $searcher
            ->setCounterId($request->getCounterId())
            ->setWithCounter()
            ->setWithPrevious()
            ->setVerified(false)
            ->defaultSort()
        ;
        $counterHistory = $this->counterHistoryService->search($searcher)->getItems()->last();
        if ($counterHistory) {
            $history->setPreviousId($counterHistory->getId());
        }

        $history->setCounterId($request->getCounterId());
        $this->counterHistoryService->save($history);
    }

    public function confirm(ConfirmRequest $request): void
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::COUNTERS_EDIT)) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            $searcher = new CounterHistorySearcher();
            $searcher
                ->setIds($request->getIds())
                ->setVerified(false)
                ->defaultSort()
            ;

            $counterHistories = $this->counterHistoryService->search($searcher)->getItems();

            foreach ($counterHistories as $history) {
                $history->setIsVerified(true);
                $history = $this->counterHistoryService->save($history);

                dispatch(new CheckClaimForCounterChangeJob($history->getId()));
            }

            DB::commit();
        }
        catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function confirmDelete(ConfirmRequest $request): void
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::COUNTERS_DROP)) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            foreach ($request->getIds() as $id) {
                $this->counterHistoryService->deleteById($id);
            }

            DB::commit();
        }
        catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::COUNTERS_DROP)) {
            abort(403);
        }

        return $this->counterHistoryService->deleteById($id);
    }
}
