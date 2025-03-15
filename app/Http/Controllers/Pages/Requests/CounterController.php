<?php declare(strict_types=1);

namespace App\Http\Controllers\Pages\Requests;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\Requests\CounterCreateRequest;
use Core\Domains\Counter\CounterLocator;
use Core\Domains\Counter\Factories\CounterHistoryFactory;
use Core\Domains\Counter\Services\CounterHistoryService;
use Core\Domains\Counter\Services\FileService;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Domains\Infra\HistoryChanges\Models\LogData;
use Core\Domains\Infra\HistoryChanges\Services\HistoryChangesService;
use Illuminate\Support\Facades\DB;

class CounterController extends Controller
{
    private CounterHistoryService $counterHistoryService;
    private CounterHistoryFactory $counterHistoryFactory;
    private FileService           $fileService;
    private HistoryChangesService $historyChangesService;

    public function __construct()
    {
        $this->counterHistoryService = CounterLocator::CounterHistoryService();
        $this->counterHistoryFactory = CounterLocator::CounterHistoryFactory();
        $this->fileService           = CounterLocator::FileService();
        $this->historyChangesService = HistoryChangesLocator::HistoryChangesService();
    }

    public function create(CounterCreateRequest $request): void
    {
        DB::beginTransaction();

        $history = $this->counterHistoryFactory->makeDefault()
            ->setValue($request->getValue())
        ;

        $history = $this->counterHistoryService->save($history);

        $this->fileService->store($request->getFile(), $history->getId());

        $historyChanges = $this->historyChangesService->makeHistory()
            ->setType(HistoryType::COUNTER)
            ->setReferenceType(HistoryType::COUNTER_HISTORY)
            ->setReferenceId($history->getId())
            ->setLog(new LogData(Event::COMMON, null, $request->getFullText()))
        ;

        $this->historyChangesService->save($historyChanges);
        DB::commit();
    }
}
