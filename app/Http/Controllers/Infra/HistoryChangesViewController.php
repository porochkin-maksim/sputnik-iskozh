<?php declare(strict_types=1);

namespace App\Http\Controllers\Infra;

use App\Http\Requests\DefaultRequest;
use App\Models\Infra\HistoryChanges;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Domains\Infra\HistoryChanges\Models\HistoryChangesSearcher;
use Core\Domains\Infra\HistoryChanges\Services\HistoryChangesService;
use Core\Requests\RequestArgumentsEnum;

class HistoryChangesViewController
{
    private const int MAX_LIMIT = 1000;

    private HistoryChangesService $historyChangesService;

    public function __construct()
    {
        $this->historyChangesService = HistoryChangesLocator::HistoryChangesService();
    }

    public function __invoke(DefaultRequest $request)
    {
        $type          = $request->getIntOrNull(RequestArgumentsEnum::TYPE);
        $primaryId     = $request->getIntOrNull(RequestArgumentsEnum::PRIMARY_ID) ?: $request->getIntOrNull('primaryId');
        $referenceType = $request->getIntOrNull(RequestArgumentsEnum::REFERENCE_TYPE) ?: $request->getIntOrNull('referenceType');
        $referenceId   = $request->getIntOrNull(RequestArgumentsEnum::REFERENCE_ID) ?: $request->getIntOrNull('referenceId');
        $limit         = min($request->getIntOrNull(RequestArgumentsEnum::LIMIT) ?? 25, self::MAX_LIMIT);
        $offset        = max($request->getIntOrNull(RequestArgumentsEnum::SKIP) ?? 0, 0);

        $searcher = new HistoryChangesSearcher();
        $searcher->setMainFilters($type, $primaryId, $referenceType, $referenceId)
            ->setSortOrderProperty(HistoryChanges::ID, SearcherInterface::SORT_ORDER_DESC)
            ->setLimit($limit)
            ->setOffset($offset);

        $historyChanges = $this->historyChangesService->search($searcher)->getItems();

        return view('admin.pages.history', compact('historyChanges', 'limit', 'offset'));
    }
}
