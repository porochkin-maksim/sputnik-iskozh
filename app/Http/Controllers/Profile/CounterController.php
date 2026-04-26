<?php declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Http\Resources\Profile\Counters\CounterHistoryListResource;
use App\Http\Resources\Profile\Counters\CounterListResource;
use App\Models\Counter\Counter;
use Core\App\Counter\CreateProfileCounterCommand;
use Core\App\Counter\UpdateCounterIncrementCommand;
use Core\App\CounterHistory\AddProfileCounterHistoryCommand;
use Core\Domains\Counter\CounterSearcher;
use Core\Domains\Counter\CounterService;
use Core\Domains\CounterHistory\CounterHistoryCollection;
use Core\Domains\CounterHistory\CounterHistorySearcher;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Domains\Infra\Uid\UidFacade;
use Core\Domains\Infra\Uid\UidTypeEnum;
use Core\Exceptions\ValidationException;
use Core\Repositories\SearcherInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use lc;
use Throwable;

class CounterController extends Controller
{

    public function __construct(
        private readonly CounterService                  $counterService,
        private readonly CounterHistoryService           $counterHistoryService,
        private readonly CreateProfileCounterCommand     $createProfileCounterCommand,
        private readonly UpdateCounterIncrementCommand   $updateCounterIncrementCommand,
        private readonly AddProfileCounterHistoryCommand $addProfileCounterHistoryCommand,
    )
    {
    }

    public function index(): View
    {
        return view('home.pages.counters.index');
    }

    public function view(string $counterUid): View
    {
        $counterId = UidFacade::findReferenceId($counterUid, UidTypeEnum::COUNTER);
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

        return view('home.pages.counters.view', compact('counter'));
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
            'counters' => new CounterListResource($result),
        ]);
    }

    /**
     * @throws Throwable
     */
    public function create(DefaultRequest $request): void
    {
        $account = lc::account();

        if ( ! $account->getId()) {
            return;
        }

        $this->createProfileCounterCommand->execute(
            $account->getId(),
            $request->getString('number'),
            abs($request->getInt('increment')),
            $request->getDateOrNull('expireAt'),
            $request->getInt('value'),
            $request->file('file'),
            $request->file('passportFile'),
        );
    }

    public function incrementSave(DefaultRequest $request): void
    {
        $this->updateCounterIncrementCommand->execute(
            $request->getInt('id'),
            $request->getInt('increment'),
            lc::account()->getId(),
        );
    }

    /**
     * @throws Throwable
     * @throws ValidationException
     */
    public function addValue(DefaultRequest $request): void
    {
        $this->addProfileCounterHistoryCommand->execute(
            $request->getInt('counter_id'),
            $request->getInt('value'),
            $request->file('file'),
            lc::account()->getId(),
        );
    }

    public function history(DefaultRequest $request): JsonResponse
    {
        $limit = 4;

        $counterId = $request->getInt('counter_id');

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
