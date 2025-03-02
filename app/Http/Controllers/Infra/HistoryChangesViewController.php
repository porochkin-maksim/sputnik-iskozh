<?php declare(strict_types=1);

namespace App\Http\Controllers\Infra;

use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Domains\Infra\HistoryChanges\Services\HistoryChangesService;

class HistoryChangesViewController
{
    private HistoryChangesService $historyChangesService;

    public function __construct()
    {
        $this->historyChangesService = HistoryChangesLocator::HistoryChangesService();
    }

    public function __invoke(int $type, int $primaryId, ?int $referenceId = null)
    {
        $historyChanges = $this->historyChangesService->search($type, $primaryId, $referenceId)->getItems();

        return view('admin.pages.history', compact('historyChanges'));
    }
}
