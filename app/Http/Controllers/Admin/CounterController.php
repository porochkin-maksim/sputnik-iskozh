<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Counters\ConfirmRequest;
use App\Http\Requests\Admin\Counters\LinkRequest;
use App\Http\Resources\Admin\Counters\CounterHistoryListResource;
use Core\Domains\Counter\CounterLocator;
use Core\Domains\Counter\Models\CounterHistorySearcher;
use Core\Domains\Counter\Services\CounterHistoryService;
use lc;
use App\Http\Controllers\Controller;
use Core\Domains\Access\Enums\PermissionEnum;
use Illuminate\Http\JsonResponse;

class CounterController extends Controller
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

        $history->setCounterId($request->getCounterId());
        $this->counterHistoryService->save($history);
    }

    public function confirm(ConfirmRequest $request): void
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::COUNTERS_EDIT)) {
            abort(403);
        }

        $searcher = new CounterHistorySearcher();
        $searcher
            ->setIds($request->getIds())
            ->setVerified(false)
            ->defaultSort()
        ;

        $counterHistories = $this->counterHistoryService->search($searcher)->getItems();

        foreach ($counterHistories as $history) {
            $history->setIsVerified(true);
            $this->counterHistoryService->save($history);
        }
    }

    public function delete(int $id): bool
    {
        if ( ! lc::roleDecorator()->can(PermissionEnum::PAYMENTS_DROP)) {
            abort(403);
        }

        return $this->counterHistoryService->deleteById($id);
    }
}
