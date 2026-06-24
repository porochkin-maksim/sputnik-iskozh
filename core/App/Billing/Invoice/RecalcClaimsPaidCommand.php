<?php declare(strict_types=1);

namespace Core\App\Billing\Invoice;

use App\Models\Billing\Claim;
use Core\Domains\Account\AccountIdEnum;
use Core\Domains\Billing\Claim\ClaimCollection;
use Core\Domains\Billing\Claim\ClaimEntity;
use Core\Domains\Billing\Claim\ClaimSearcher;
use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\Invoice\InvoiceEntity;
use Core\Domains\Billing\Invoice\InvoiceSearcher;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Payment\PaymentService;
use Core\Domains\Billing\Transaction\TransactionCollection;
use Core\Domains\Billing\Transaction\TransactionEntity;
use Core\Domains\Billing\Transaction\TransactionFactory;
use Core\Domains\Billing\Transaction\TransactionSearcher;
use Core\Domains\Billing\Transaction\TransactionService;
use Core\Repositories\SearcherInterface;

readonly class RecalcClaimsPaidCommand
{
    public function __construct(
        private InvoiceService     $invoiceService,
        private ClaimService       $claimService,
        private TransactionService $transactionService,
        private PaymentService     $paymentService,
    )
    {
    }

    public function execute(int $invoiceId): void
    {
        $invoice = $this->invoiceService->search(
            new InvoiceSearcher()->setId($invoiceId)->setWithClaims(),
        )->getItems()->first();

        if ($invoice === null) {
            return;
        }

        $claims = $this->claimService->search(new ClaimSearcher()
            ->setInvoiceId($invoiceId)
            ->setWithService()
            ->setSortOrderProperty(Claim::SERVICE_ID, SearcherInterface::SORT_ORDER_ASC),
        )->getItems()->sortByServiceTypes();

        if ($claims->isEmpty()) {
            $this->recalcInvoice($invoice, new ClaimCollection());

            return;
        }

        // пересчитываем cost из tariff × quantity
        foreach ($claims as $claim) {
            $claim->setQuantity($claim->getQuantity() ? : 1.00);
            $claim->setCost((float) $claim->getTariff() * (float) $claim->getQuantity());
        }

        // существующая оплата из уже распределённых транзакций
        $claimIds    = array_values(array_filter(
            $claims->map(fn(ClaimEntity $c) => $c->getId())->toArray(),
        ));
        $allocatedTx = $this->transactionService->search(
            new TransactionSearcher()->setClaimIds($claimIds),
        )->getItems();

        $paidByClaim = [];
        foreach ($allocatedTx as $tx) {
            $cid               = $tx->getClaimId();
            $paidByClaim[$cid] = ($paidByClaim[$cid] ?? 0.0) + (float) $tx->getCost();
        }

        // если стоимость уменьшилась — избыток отпускаем в unallocated
        foreach ($claims as $claim) {
            $existingTotal = $paidByClaim[$claim->getId()] ?? 0.0;
            $excess        = (float) $existingTotal - (float) $claim->getCost();

            if ($excess <= 0) {
                $claim->setPaid($existingTotal);
                continue;
            }

            $claimTx = $this->transactionService->search(new TransactionSearcher()
                ->setClaimIds([$claim->getId()])
                ->setSortOrderPropertyIdDesc(),
            )->getItems();

            foreach ($claimTx as $tx) {
                if ($excess <= 0) {
                    break;
                }

                $released      = $this->releaseFromClaim($tx, $excess);
                $excess        -= $released;
                $existingTotal -= $released;
            }

            $claim->setPaid($existingTotal);
        }

        // распределяем нераспределённые транзакции участка
        $paymentIds  = $this->paymentService->getVerifiedByAccount($invoice->getAccountId())->getIds();
        $unallocated = $paymentIds !== []
            ? $this->transactionService->getUnallocatedBypaymentIds($paymentIds)
            : new TransactionCollection();

        if ( ! $unallocated->isEmpty()) {
            $transactionFactory = new TransactionFactory();

            foreach ($claims as $claim) {
                $remaining = (float) $claim->getCost() - (float) $claim->getPaid();
                if ($remaining <= 0) {
                    continue;
                }

                foreach ($unallocated as $tx) {
                    if ($remaining <= 0) {
                        break;
                    }
                    if ($tx->getClaimId() !== null || (float) $tx->getCost() <= 0) {
                        continue;
                    }

                    $txCost = (float) $tx->getCost();
                    $toPay  = min($remaining, $txCost);

                    if ($toPay >= $txCost) {
                        $tx->setClaimId($claim->getId());
                        $this->transactionService->save($tx);
                    }
                    else {
                        $tx->setCost($txCost - $toPay);
                        $this->transactionService->save($tx);

                        $newTx = $transactionFactory->makeDefault()
                            ->setPaymentId($tx->getPaymentId())
                            ->setClaimId($claim->getId())
                            ->setCost($toPay)
                        ;
                        $this->transactionService->save($newTx);
                    }

                    $remaining -= $toPay;
                    $claim->setPaid(($claim->getPaid() ?? 0.0) + $toPay);
                }
            }
        }

        $this->claimService->saveCollection($claims);
        $this->recalcInvoice($invoice, $claims);
    }

    /**
     * Отпускает сумму из распределённой транзакции обратно в unallocated.
     * Если у того же платежа уже есть нераспределённая транзакция — сливает в неё,
     * вместо создания новой orphan-транзакции.
     */
    private function releaseFromClaim(TransactionEntity $tx, float $amount): float
    {
        $paymentId = $tx->getPaymentId();
        $txCost    = (float) $tx->getCost();
        $released  = min($amount, $txCost);

        if ($released <= 0) {
            return 0.0;
        }

        $existing = $this->transactionService->search(new TransactionSearcher()
            ->setPaymentId($paymentId)
            ->setClaimId(null),
        )->getItems()->first();

        if ($existing) {
            $existing->setCost((float) $existing->getCost() + $released);
            $this->transactionService->save($existing);

            if ($released >= $txCost) {
                $this->transactionService->deleteById($tx->getId());
            }
            else {
                $tx->setCost($txCost - $released);
                $this->transactionService->save($tx);
            }
        }
        else {
            if ($released >= $txCost) {
                $tx->setClaimId(null);
                $this->transactionService->save($tx);
            }
            else {
                $tx->setCost($txCost - $released);
                $this->transactionService->save($tx);

                $newTx = new TransactionFactory()->makeDefault()
                    ->setPaymentId($paymentId)
                    ->setClaimId(null)
                    ->setCost($released)
                ;
                $this->transactionService->save($newTx);
            }
        }

        return $released;
    }

    private function recalcInvoice(InvoiceEntity $invoice, ClaimCollection $claims): void
    {
        $totalCost  = 0.0;
        $totalPaid  = 0.0;
        $debtAmount = 0.0;
        foreach ($claims as $claim) {
            $totalCost += (float) $claim->getCost();
            $totalPaid += (float) $claim->getPaid();
            if ($claim->getService()?->getType()?->isDebt()) {
                $debtAmount += (float) $claim->getCost();
            }
        }

        if ($invoice->getAccountId() === AccountIdEnum::SNT->value) {
            $rounding    = 0.0;
            $invoiceCost = round($totalCost, 2);
        }
        else {
            $cents = $totalCost - (float) (int) $totalCost;
            if ($cents >= 0.50) {
                $rounding    = 1.0 - $cents;
                $invoiceCost = (float) ((int) $totalCost + 1);
            }
            else {
                $rounding    = -$cents;
                $invoiceCost = (float) (int) $totalCost;
            }
        }

        // debt пропорционально округлённой стоимости
        $adjustedDebt = $totalCost > 0
            ? round($debtAmount / $totalCost * $invoiceCost, 2)
            : 0.0;

        $invoice->setCost($invoiceCost);
        $invoice->setPaid($totalPaid);
        $invoice->setDebt($adjustedDebt);
        $invoice->setRounding($rounding);

        $this->invoiceService->save($invoice);
    }
}
