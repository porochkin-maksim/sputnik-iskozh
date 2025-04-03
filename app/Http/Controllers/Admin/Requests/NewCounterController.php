<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin\Requests;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Counters\ConfirmRequest;
use App\Http\Requests\Admin\Counters\LinkRequest;
use App\Http\Resources\Admin\Counters\CounterHistoryListResource;
use Core\Domains\Access\Enums\PermissionEnum;
use Core\Domains\Billing\Jobs\CheckTransactionForCounterChangeJob;
use Core\Domains\Counter\CounterLocator;
use Core\Domains\Counter\Models\CounterHistorySearcher;
use Core\Domains\Counter\Services\CounterHistoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use lc;
use Throwable;

class NewCounterController extends Controller
{
    private CounterHistoryService $counterHistoryService;

    public function __construct()
    {
        $this->counterHistoryService = CounterLocator::CounterHistoryService();
    }

    public function list(): JsonResponse
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::COUNTERS_VIEW)) {
            abort(403);
        }

        $searcher = new CounterHistorySearcher();
        $searcher
            ->setWithCounter()
            ->setWithPrevious()
            ->setVerified(false)
            ->defaultSort()
        ;

        $counterHistories = $this->counterHistoryService->search($searcher);
        $counterHistories = $counterHistories->getItems();
        if ($counterHistories->hasUnlinked()) {
            $counterHistories = $counterHistories->getUnlinked();
        }

        return response()->json(new CounterHistoryListResource($counterHistories));
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

                dispatch(new CheckTransactionForCounterChangeJob($history->getId()));
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
