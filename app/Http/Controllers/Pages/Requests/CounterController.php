<?php declare(strict_types=1);

namespace App\Http\Controllers\Pages\Requests;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\Requests\CounterCreateRequest;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Services\AccountService;
use Core\Domains\Counter\CounterLocator;
use Core\Domains\Counter\Factories\CounterHistoryFactory;
use Core\Domains\Counter\Models\CounterDTO;
use Core\Domains\Counter\Services\CounterHistoryService;
use Core\Domains\Counter\Services\CounterService;
use Core\Domains\Counter\Services\FileService;
use Core\Domains\Infra\HistoryChanges\Enums\Event;
use Core\Domains\Infra\HistoryChanges\Enums\HistoryType;
use Core\Domains\Infra\HistoryChanges\HistoryChangesLocator;
use Core\Domains\Infra\HistoryChanges\Models\LogData;
use Core\Domains\Infra\HistoryChanges\Services\HistoryChangesService;
use Exception;
use Illuminate\Support\Facades\DB;

class CounterController extends Controller
{
    private CounterService        $counterService;
    private CounterHistoryService $counterHistoryService;
    private CounterHistoryFactory $counterHistoryFactory;
    private FileService           $fileService;
    private HistoryChangesService $historyChangesService;
    private AccountService        $accountService;

    public function __construct()
    {
        $this->counterService        = CounterLocator::CounterService();
        $this->counterHistoryService = CounterLocator::CounterHistoryService();
        $this->counterHistoryFactory = CounterLocator::CounterHistoryFactory();
        $this->fileService           = CounterLocator::FileService();
        $this->historyChangesService = HistoryChangesLocator::HistoryChangesService();
        $this->accountService        = AccountLocator::AccountService();
    }

    public function create(CounterCreateRequest $request): void
    {
        DB::beginTransaction();
        try {
            $history = $this->counterHistoryFactory->makeDefault()
                ->setValue($request->getValue())
            ;

            if ($request->getAccount()) {
                $account = $this->accountService->findByNumber($request->getAccount());
                if ($account) {
                    $counters = $this->counterService->getByAccountId($account->getId());
                    $counter  = null;
                    if ($request->getCounter()) {
                        $counter = $counters->filter(function (CounterDTO $counter) use ($request) {
                            return $counter->getNumber() === $request->getCounter();
                        });
                    }
                    $counter = $counter ?? $counters->getInvoicing()->first();

                    if ($counter) {
                        $history->setCounterId($counter->getId());
                    }
                }
            }

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
        catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
