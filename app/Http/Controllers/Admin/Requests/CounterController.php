<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\Requests;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Admin\Counters\CounterHistoryListResource;
use Core\App\CounterHistory\ConfirmCounterHistoriesCommand;
use Core\App\CounterHistory\DeleteCounterHistoriesCommand;
use Core\App\CounterHistory\LinkCounterHistoryCommand;
use App\Models\Account\Account;
use App\Models\Counter\Counter;
use App\Models\Counter\CounterHistory;
use Core\Repositories\SearcherInterface;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Account\AccountSearcher;
use Core\Domains\Account\AccountService;
use Core\Domains\Counter\CounterSearcher;
use Core\Domains\Counter\CounterService;
use Core\Domains\CounterHistory\CounterHistorySearcher;
use Core\Domains\CounterHistory\CounterHistoryService;
use Illuminate\Http\JsonResponse;
use lc;
use Throwable;

class CounterController extends Controller
{

    public function __construct(
        private readonly CounterHistoryService          $counterHistoryService,
        private readonly CounterService                 $counterService,
        private readonly AccountService                 $accountService,
        private readonly LinkCounterHistoryCommand      $linkCounterHistoryCommand,
        private readonly ConfirmCounterHistoriesCommand $confirmCounterHistoriesCommand,
        private readonly DeleteCounterHistoriesCommand  $deleteCounterHistoriesCommand,
    )
    {
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

    /**
     * @throws Throwable
     */
    public function link(DefaultRequest $request): void
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::COUNTERS_EDIT)) {
            abort(403);
        }

        $this->linkCounterHistoryCommand->execute($request->getInt('id'), $request->getInt('counter_id'));
    }

    /**
     * @throws Throwable
     */
    public function confirm(DefaultRequest $request): void
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::COUNTERS_EDIT)) {
            abort(403);
        }

        $this->confirmCounterHistoriesCommand->execute(array_map('intval', $request->getArray('ids')));
    }

    /**
     * @throws Throwable
     */
    public function confirmDelete(DefaultRequest $request): void
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::COUNTERS_DROP)) {
            abort(403);
        }

        $this->deleteCounterHistoriesCommand->execute(array_map('intval', $request->getArray('ids')));
    }

    public function delete(int $id): bool
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::COUNTERS_DROP)) {
            abort(403);
        }

        return $this->counterHistoryService->deleteById($id);
    }
}
