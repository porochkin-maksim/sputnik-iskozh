<?php declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\Counters\AddHistoryRequest;
use App\Http\Requests\Profile\Counters\CreateRequest;
use App\Http\Resources\Profile\Counters\CounterListResource;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Services\AccountService;
use Core\Domains\Counter\CounterLocator;
use Core\Domains\Counter\Factories\CounterFactory;
use Core\Domains\Counter\Factories\CounterHistoryFactory;
use Core\Domains\Counter\Services\CounterHistoryService;
use Core\Domains\Counter\Services\CounterService;
use Core\Domains\Counter\Services\FileService;
use Core\Resources\Views\ViewNames;
use Core\Responses\ResponsesEnum;
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

    public function list(): JsonResponse
    {
        $result = $this->counterService->getByAccountId(lc::account()->getId());

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

    public function addValue(AddHistoryRequest $request): void
    {
        DB::beginTransaction();
        $counter = $this->counterService->getById($request->getCounterId());

        if (!$counter) {
            abort(404);
        }

        if ($counter->getAccountId() !== lc::account()->getId()) {
            abort(403);
        }

        $history = $this->counterHistoryFactory->makeDefault()
            ->setPreviousId($counter->getHistoryCollection()->last()?->getId())
            ->setCounterId($counter->getId())
            ->setValue($request->getValue())
        ;

        $history = $this->counterHistoryService->save($history);

        $this->fileService->store($request->getFile(), $history->getId());
        DB::commit();
    }
}
