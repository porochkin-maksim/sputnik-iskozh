<?php declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Requests\Profile\Counters\AddHistoryRequest;
use App\Http\Requests\Profile\Counters\CreateRequest;
use App\Http\Resources\Profile\Counters\CounterHistoryListResource;
use App\Http\Resources\Profile\Counters\CounterListResource;
use App\Models\Counter\Counter;
use App\Models\Counter\CounterHistory;
use Core\Db\Searcher\SearcherInterface;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Services\AccountService;
use Core\Domains\Counter\Collections\CounterHistoryCollection;
use Core\Domains\Counter\CounterLocator;
use Core\Domains\Counter\Factories\CounterFactory;
use Core\Domains\Counter\Factories\CounterHistoryFactory;
use Core\Domains\Counter\Models\CounterHistorySearcher;
use Core\Domains\Counter\Models\CounterSearcher;
use Core\Domains\Counter\Services\CounterHistoryService;
use Core\Domains\Counter\Services\CounterService;
use Core\Domains\Counter\Services\FileService;
use Core\Domains\Infra\Uid\UidFacade;
use Core\Requests\RequestArgumentsEnum;
use Core\Resources\Views\ViewNames;
use Core\Responses\ResponsesEnum;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use lc;

class CounterController extends Controller
{
    private AccountService        $accountService;
    private CounterService        $counterService;
    private CounterFactory        $counterFactory;
    private CounterHistoryService $counterHistoryService;
    private CounterHistoryFactory $counterHistoryFactory;
    private FileService           $fileService;

    public function __construct()
    {
        $this->accountService        = AccountLocator::AccountService();
        $this->counterService        = CounterLocator::CounterService();
        $this->counterFactory        = CounterLocator::CounterFactory();
        $this->counterHistoryService = CounterLocator::CounterHistoryService();
        $this->counterHistoryFactory = CounterLocator::CounterHistoryFactory();
        $this->fileService           = CounterLocator::FileService();
    }

    public function index(): View
    {
        return view(ViewNames::PAGES_PROFILE_COUNTERS);
    }

    public function view(string $counterUid): View
    {
        $counterId = UidFacade::findReferenceId($counterUid);
        if ( ! $counterId) {
            abort(404);
        }

        $counterSearcher = new CounterSearcher();
        $counterSearcher
            ->setAccountId(lc::account()->getId())
            ->setId($counterId)
            ->setLimit(1)
        ;
        $counter = $this->counterService->search($counterSearcher)->getItems()->first();

        if ( ! $counter) {
            abort(404);
        }

        return view(ViewNames::PAGES_PROFILE_COUNTERS_VIEW, compact('counter'));
    }

    public function list(): JsonResponse
    {
        $counterSearcher = new CounterSearcher();
        $counterSearcher
            ->setAccountId(lc::account()->getId())
            ->setSortOrderProperty(Counter::IS_INVOICING, SearcherInterface::SORT_ORDER_DESC)
            ->setSortOrderProperty(Counter::ID, SearcherInterface::SORT_ORDER_DESC)
        ;

        $result = $this->counterService->search($counterSearcher)->getItems();

        foreach ($result as $counter) {
            $lastHistory = $this->counterHistoryService->getLastByCounterId($counter->getId());
            $counter->setHistoryCollection(new CounterHistoryCollection([$lastHistory]));
        }

        return response()->json([
            ResponsesEnum::COUNTERS => new CounterListResource($result),
        ]);
    }

    public function create(CreateRequest $request): void
    {
        $account = $this->accountService->getByUserId(Auth::id());

        if ($account) {
            DB::beginTransaction();
            $counter = $this->counterFactory->makeDefault()
                ->setNumber($request->getNumber())
                ->setAccountId($account->getId())
                ->setIncrement($request->getIncrement())
            ;

            $counter = $this->counterService->save($counter);

            $history = $this->counterHistoryFactory->makeDefault()
                ->setCounterId($counter->getId())
                ->setValue($request->getValue())
            ;
            $history = $this->counterHistoryService->save($history);

            $this->fileService->store($request->getFile(), $history->getId());
            DB::commit();
        }
    }

    public function incrementSave(DefaultRequest $request): void
    {
        $counter = $this->counterService->getById($request->getInt('id'));

        if ($counter && $counter->getAccountId() === lc::account()->getId()) {
            DB::beginTransaction();
            $counter->setIncrement($request->getInt(RequestArgumentsEnum::INCREMENT));

            $this->counterService->save($counter);
            DB::commit();
        }
    }

    public function addValue(AddHistoryRequest $request): void
    {
        DB::beginTransaction();
        try {
            $counter = $this->counterService->getById($request->getCounterId());

            if ( ! $counter) {
                abort(404);
            }

            if ($counter->getAccountId() !== lc::account()->getId()) {
                abort(403);
            }

            $lastHistory = $this->counterHistoryService->getLastByCounterId($counter->getId());

            $history = $this->counterHistoryFactory->makeDefault()
                ->setPreviousId($lastHistory?->getId())
                ->setPreviousValue($lastHistory?->getValue())
                ->setCounterId($counter->getId())
                ->setValue($request->getValue())
            ;

            $history = $this->counterHistoryService->save($history);

            $this->fileService->store($request->getFile(), $history->getId());
            DB::commit();

        }
        catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function history(DefaultRequest $request): JsonResponse
    {
        $limit = 4;

        $counterId = $request->getInt(RequestArgumentsEnum::COUNTER_ID);

        $counter = $this->counterService->getById($counterId);
        if ( ! $counter || $counter->getAccountId() !== lc::account()->getId()) {
            abort(403);
        }

        $searcher = CounterHistorySearcher::make()
            ->setCounterId($counterId)
            ->setLimit($limit)
            ->setOffset($request->getOffset())
            ->setWithClaim()
            ->descSort()
        ;

        $response = $this->counterHistoryService->search($searcher);

        return response()->json([
            'histories' => new CounterHistoryListResource($response->getItems()),
            'total'     => $response->getTotal(),
            'limit'     => $limit,
        ]);
    }
}
