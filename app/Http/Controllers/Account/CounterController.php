<?php declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Core\Domains\Account\AccountLocator;
use Core\Domains\Account\Services\AccountService;
use Core\Domains\Counter\CounterLocator;
use Core\Domains\Counter\Requests\SaveRequest;
use Core\Domains\Counter\Services\CounterService;
use Core\Responses\ResponsesEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CounterController extends Controller
{
    private AccountService $accountService;
    private CounterService $counterService;

    public function __construct()
    {
        $this->accountService = AccountLocator::AccountService();
        $this->counterService = CounterLocator::CounterService();
    }

    public function list(): JsonResponse
    {
        $result = null;

        $account = $this->accountService->getByUserId(Auth::id());

        if ($account) {
            $result = $this->counterService->getByAccountId($account->getId());
        }

        return response()->json([
            ResponsesEnum::COUNTERS => $result,
            ResponsesEnum::COUNTER  => $result?->getPrimary(),
        ]);
    }

    public function save(SaveRequest $request): void
    {
        $account = $this->accountService->getByUserId(Auth::id());

        if ($account) {
            $counter = $request->dto();
            $counter->setAccountId($account->getId());

            $this->counterService->save($counter);
        }
    }
}
