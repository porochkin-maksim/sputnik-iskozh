<?php declare(strict_types=1);

namespace App\Http\Controllers\Infra;

use App\Http\Requests\DefaultRequest;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Domains\Infra\HistoryChanges\Services\HistoryChangesService;
use Core\Requests\RequestArgumentsEnum;

class HistoryChangesViewController
{
    private HistoryChangesService $historyChangesService;

    public function __construct()
    {
        $this->historyChangesService = HistoryChangesLocator::HistoryChangesService();
    }

    public function __invoke(DefaultRequest $request)
    {
        $type           = $request->getIntOrNull(RequestArgumentsEnum::TYPE);
        $primaryId      = $request->getIntOrNull(RequestArgumentsEnum::PRIMARY_ID);
        $referenceType  = $request->getIntOrNull(RequestArgumentsEnum::REFERENCE_TYPE);
        $referenceId    = $request->getIntOrNull(RequestArgumentsEnum::REFERENCE_ID);

        $historyChanges = $this->historyChangesService->search($type, $primaryId, $referenceType, $referenceId)->getItems();

        return view('admin.pages.history', compact('historyChanges'));
    }
}
