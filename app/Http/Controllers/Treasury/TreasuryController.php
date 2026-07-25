<?php declare(strict_types=1);

namespace App\Http\Controllers\Treasury;

use App\Http\Controllers\Controller;
use App\Http\Requests\DefaultRequest;
use App\Services\Money\MoneyService;
use Core\App\CounterHistory\CreateCounterClaimCommand;
use Core\Domains\Access\PermissionEnum;
use Core\Domains\Account\AccountRepositoryInterface;
use Core\Domains\Account\AccountSearcher;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Payment\PaymentFactory;
use Core\Domains\Billing\Payment\PaymentService;
use Core\Domains\Billing\Period\PeriodService;
use Core\Domains\Billing\Transaction\TransactionFactory;
use Core\Domains\Billing\Transaction\TransactionService;
use Core\App\Billing\Payment\PayCommand;
use Core\Domains\Counter\CounterFactory;
use Core\Domains\Counter\CounterService;
use Core\Domains\CounterHistory\CounterHistoryFactory;
use Core\Domains\CounterHistory\CounterHistoryService;
use Core\Exceptions\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use lc;

class TreasuryController extends Controller
{
    public function __construct(
        private readonly AccountRepositoryInterface $accountRepository,
        private readonly InvoiceService             $invoiceService,
        private readonly PaymentService             $paymentService,
        private readonly PaymentFactory             $paymentFactory,
        private readonly PeriodService              $periodService,
        private readonly TransactionService         $transactionService,
        private readonly TransactionFactory         $transactionFactory,
        private readonly CounterService             $counterService,
        private readonly CounterFactory             $counterFactory,
        private readonly CounterHistoryService      $counterHistoryService,
        private readonly CounterHistoryFactory      $counterHistoryFactory,
        private readonly PayCommand                 $payCommand,
        private readonly CreateCounterClaimCommand  $createCounterClaimCommand,
    )
    {
    }

    public function index(): View
    {
        return view('pages.treasury.index');
    }

    public function search(DefaultRequest $request): JsonResponse
    {
        $query = $request->getStringOrNull('query');

        $searcher = new AccountSearcher()->setLimit(3);

        if ($query) {
            $searcher->setNumberLike($query);
        }

        $response = $this->accountRepository->search($searcher);

        $accounts = [];
        foreach ($response->getItems() as $account) {
            $accounts[] = [
                'id'      => $account->getId(),
                'number'  => $account->getNumber(),
                'balance' => $this->transactionService->getBalanceByAccountId($account->getId()),
            ];
        }

        return response()->json(['accounts' => $accounts]);
    }

    public function charges(int $accountId): JsonResponse
    {
        $period   = $this->periodService->getOpenPeriods()->first();
        $invoices = $this->invoiceService->getChargesByAccountIdAndPeriodId($accountId, $period->getId());

        $result = [];
        foreach ($invoices as $invoice) {
            $claims = [];
            foreach ($invoice->getClaims() ?? [] as $claim) {
                $serviceName = $claim->getService()?->getName() ?? '';
                $name        = $claim->getName() ? : $serviceName;

                $claims[] = [
                    'id'       => $claim->getId(),
                    'name'     => $name,
                    'quantity' => $claim->getQuantity(),
                    'tariff'   => $claim->getTariff(),
                    'cost'     => $claim->getCost(),
                    'paid'     => $claim->getPaid(),
                    'delta'    => $claim->getDelta(),
                ];
            }

            $cost        = $invoice->getCost();
            $paid        = $invoice->getPaid();
            $rounding    = $invoice->getRounding();
            $debtCost    = $cost === 0.0 ? 0.0 : $invoice->getDebt();
            $invoiceCost = MoneyService::subtract($cost, $rounding);

            $detailCost = [
                'total'    => $invoiceCost,
                'debt'     => $debtCost,
                'main'     => MoneyService::subtract($invoiceCost, $debtCost),
                'rounding' => $rounding,
            ];

            $result[] = [
                'id'         => $invoice->getId(),
                'periodName' => ($invoice->getPeriod()?->getName() . ' ' . ($invoice->getType()?->isRegular() ? 'Основной' : $invoice->getName())),
                'cost'       => $cost,
                'paid'       => $paid,
                'delta'      => $invoice->getDelta(),
                'rounding'   => $rounding,
                'detailCost' => $detailCost,
                'isPaid'     => $invoice->isPaid(),
                'claims'     => $claims,
            ];
        }

        return response()->json(['invoices' => $result]);
    }

    public function counters(int $accountId): JsonResponse
    {
        $counters = $this->counterService->getByAccountId($accountId);

        $result = [];
        foreach ($counters as $counter) {
            $lastHistory = $this->counterHistoryService->getLastByCounterId($counter->getId());

            $result[] = [
                'id'          => $counter->getId(),
                'number'      => $counter->getNumber(),
                'isInvoicing' => $counter->isInvoicing(),
                'lastValue'   => $lastHistory?->getValue(),
                'lastDate'    => $lastHistory?->getDate()?->format('d.m.Y'),
            ];
        }

        return response()->json(['counters' => $result]);
    }

    public function createCounter(DefaultRequest $request): JsonResponse
    {
        $accountId     = $request->getIntOrNull('account_id');
        $number        = $request->getStringOrNull('number');
        $currentValue  = $request->getIntOrNull('value');
        $previousValue = $request->getIntOrNull('previous_value');

        if ( ! $accountId || ! $number || ! $currentValue) {
            return response()->json(['message' => 'Неверные параметры'], 422);
        }

        $counter = $this->counterFactory->makeDefault()
            ->setAccountId($accountId)
            ->setNumber($number)
        ;
        $counter = $this->counterService->save($counter);

        if ($previousValue) {
            $prevHistory = $this->counterHistoryFactory->makeDefault()
                ->setCounterId($counter->getId())
                ->setValue($previousValue)
            ;
            $this->counterHistoryService->save($prevHistory);
        }

        $history = $this->counterHistoryFactory->makeDefault()
            ->setPreviousId(isset($prevHistory) ? $prevHistory->getId() : null)
            ->setPreviousValue(isset($prevHistory) ? $prevHistory->getValue() : null)
            ->setCounterId($counter->getId())
            ->setValue($currentValue)
        ;
        $this->counterHistoryService->save($history);

        return response()->json(['success' => true]);
    }

    public function addCounterValue(DefaultRequest $request): JsonResponse
    {
        $counterId = $request->getIntOrNull('counter_id');
        $value     = $request->getIntOrNull('value');

        if ( ! $counterId || ! $value) {
            return response()->json(['message' => 'Неверные параметры'], 422);
        }

        $counter = $this->counterService->getById($counterId);
        if ( ! $counter) {
            return response()->json(['message' => 'Счётчик не найден'], 404);
        }

        $lastHistory = $this->counterHistoryService->getLastByCounterId($counterId);

        $history = $this->counterHistoryFactory->makeDefault()
            ->setPreviousId($lastHistory?->getId())
            ->setPreviousValue($lastHistory?->getValue())
            ->setCounterId($counterId)
            ->setValue($value)
            ->setIsVerified(true)
        ;

        $history = $this->counterHistoryService->save($history);

        if ($counter->isInvoicing()) {
            $this->createCounterClaimCommand->execute($history->getId());
        }

        return response()->json(['success' => true]);
    }

    public function updateCounter(DefaultRequest $request): JsonResponse
    {
        $counterId   = $request->getIntOrNull('counter_id');
        $number      = $request->getStringOrNull('number');
        $isInvoicing = $request->getBool('is_invoicing');

        if ( ! $counterId || ! $number) {
            return response()->json(['message' => 'Неверные параметры'], 422);
        }

        $counter = $this->counterService->getById($counterId);
        if ( ! $counter) {
            return response()->json(['message' => 'Счётчик не найден'], 404);
        }

        $counter
            ->setNumber($number)
            ->setIsInvoicing($isInvoicing)
        ;
        $this->counterService->save($counter);

        return response()->json(['success' => true]);
    }

    public function pay(DefaultRequest $request): JsonResponse
    {
        $accountId   = $request->getIntOrNull('account_id');
        $amount      = $request->getFloat('amount');
        $allocations = $request->getArray('allocations', []);

        if ( ! $accountId) {
            return response()->json(['message' => 'Неверные параметры'], 422);
        }

        $paymentId = null;

        if ($amount > 0) {
            $payment = $this->paymentFactory->makeDefault();
            $payment
                ->setAccountId($accountId)
                ->setCost($amount)
                ->setVerified(true)
                ->setModerated(true)
            ;
            $payment   = $this->paymentService->save($payment);
            $paymentId = $payment->getId();

            $remainder = $this->transactionFactory->makeDefault()
                ->setPaymentId($paymentId)
                ->setClaimId(null)
                ->setCost($amount)
            ;
            $this->transactionService->save($remainder);
        }

        try {
            foreach ($allocations as $alloc) {
                $claimId     = (int) ($alloc['claim_id'] ?? 0);
                $allocAmount = (float) ($alloc['amount'] ?? 0);
                if ($claimId <= 0 || $allocAmount <= 0) {
                    continue;
                }
                $this->payCommand->payClaim($claimId, $allocAmount);
            }
        }
        catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true, 'paymentId' => $paymentId]);
    }
}
