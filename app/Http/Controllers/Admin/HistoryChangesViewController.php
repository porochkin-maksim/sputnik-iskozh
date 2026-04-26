<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DefaultRequest;
use App\Models\Infra\HistoryChanges;
use Core\Domains\HistoryChanges\HistoryChangesSearcher;
use Core\Domains\HistoryChanges\HistoryChangesService;
use Core\Repositories\SearcherInterface;

class HistoryChangesViewController
{
    private const int MAX_LIMIT = 1000;

    public function __construct(
        private readonly HistoryChangesService $historyChangesService,
    )
    {
    }

    public function __invoke(DefaultRequest $request)
    {
        $type          = $request->getIntOrNull('type');
        $primaryId     = $request->getIntOrNull('primary_id') ? : $request->getIntOrNull('primaryId');
        $referenceType = $request->getIntOrNull('reference_type') ? : $request->getIntOrNull('referenceType');
        $referenceId   = $request->getIntOrNull('reference_id') ? : $request->getIntOrNull('referenceId');
        $limit         = min($request->getIntOrNull('limit') ?? 25, self::MAX_LIMIT);
        $offset        = max($request->getIntOrNull('skip') ?? 0, 0);

        $searcher = new HistoryChangesSearcher();
        $searcher->setMainFilters($type, $primaryId, $referenceType, $referenceId)
            ->setSortOrderProperty(HistoryChanges::ID, SearcherInterface::SORT_ORDER_DESC)
            ->setLimit($limit)
            ->setOffset($offset)
        ;

        $historyChanges = $this->historyChangesService->search($searcher)->getItems();

        return view('admin.pages.history', compact('historyChanges', 'limit', 'offset'));
    }
}
