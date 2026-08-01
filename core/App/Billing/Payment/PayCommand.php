<?php declare(strict_types=1);

namespace Core\App\Billing\Payment;

use Core\Domains\Billing\Claim\ClaimService;
use Core\Domains\Billing\Invoice\InvoiceService;
use Core\Domains\Billing\Invoice\InvoiceTypeEnum;
use Core\Domains\Billing\Payment\PaymentService;
use Core\Domains\Billing\Transaction\TransactionCollection;
use Core\Domains\Billing\Transaction\TransactionFactory;
use Core\Domains\Billing\Transaction\TransactionService;
use Core\Exceptions\ValidationException;

readonly class PayCommand
{
    public function __construct(
        private PaymentService     $paymentService,
        private TransactionService $transactionService,
        private TransactionFactory $transactionFactory,
        private ClaimService       $claimService,
        private InvoiceService     $invoiceService,
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function payClaim(int $claimId, float $cost): void
    {
        if ($cost <= 0) {
            throw new ValidationException([], 'Сумма оплаты должна быть положительной');
        }

        $claim = $this->claimService->getById($claimId);
        if ( ! $claim || ! $claim->getInvoiceId()) {
            throw new ValidationException([], 'Услуга не найдена');
        }

        $maxPay = max(0.0, ($claim->getCost() ?? 0.0) - ($claim->getPaid() ?? 0.0));
        if ($cost > $maxPay) {
            throw new ValidationException([], 'Сумма оплаты превышает остаток по услуге');
        }

        $invoice = $this->invoiceService->getById($claim->getInvoiceId());
        if ( ! $invoice) {
            throw new ValidationException([], 'Счёт не найден');
        }

        $paymentIds = $this->paymentService->getVerifiedByAccount($invoice->getAccountId())->getIds();
        if ($paymentIds === []) {
            throw new ValidationException([], 'Нет подтверждённых платежей для оплаты');
        }

        $unallocated = $this->transactionService->getUnallocatedBypaymentIds($paymentIds);
        if ($unallocated->getTotalCost() < $cost) {
            throw new ValidationException([], 'Недостаточно свободных средств на счету');
        }

        $consumed = $this->consumeUnallocated($unallocated, $claimId, $cost);
        if ($consumed > 0) {
            $claim->setPaid(($claim->getPaid() ?? 0.0) + $consumed);
            $this->claimService->save($claim);
        }

        $this->recalcInvoicePaid($invoice->getId());
    }

    /**
     * @throws ValidationException
     */
    public function payAll(int $invoiceId): void
    {
        $invoice = $this->invoiceService->getById($invoiceId);
        if ( ! $invoice) {
            throw new ValidationException([], 'Счёт не найден');
        }

        if ($invoice->getType() === InvoiceTypeEnum::OUTCOME) {
            return;
        }
        $paymentIds = $this->paymentService->getVerifiedByAccount($invoice->getAccountId())->getIds();
        if ($paymentIds === []) {
            return;
        }

        $unallocated = $this->transactionService->getUnallocatedBypaymentIds($paymentIds);
        if ($unallocated->isEmpty()) {
            return;
        }

        $claims = $this->claimService->getByInvoiceIdSorted($invoiceId)
            ->sortByServiceTypes()
        ;

        foreach ($claims as $claim) {
            $remaining = ($claim->getCost() ?? 0.0) - ($claim->getPaid() ?? 0.0);
            if ($remaining <= 0) {
                continue;
            }

            $consumed = $this->consumeUnallocated($unallocated, $claim->getId(), $remaining);
            if ($consumed > 0) {
                $claim->setPaid(($claim->getPaid() ?? 0.0) + $consumed);
                $this->claimService->save($claim);
            }

            if ($remaining - $consumed > 0.001) {
                break;
            }
        }

        $this->recalcInvoicePaid($invoiceId);
    }

    private function consumeUnallocated(
        TransactionCollection $unallocated,
        int                   $claimId,
        float                 $needed,
    ): float {
        $consumed = 0.0;
        foreach ($unallocated as $tx) {
            if ($needed <= 0) {
                break;
            }
            if ($tx->getClaimId() !== null || (float) $tx->getCost() <= 0) {
                continue;
            }

            $txCost = (float) $tx->getCost();
            $toPay  = (float) min($needed, $txCost);

            if ($toPay >= $txCost) {
                $tx->setClaimId($claimId);
                $this->transactionService->save($tx);
            }
            else {
                $tx->setCost($txCost - $toPay);
                $this->transactionService->save($tx);

                $newTx = $this->transactionFactory->makeDefault()
                    ->setPaymentId($tx->getPaymentId())
                    ->setClaimId($claimId)
                    ->setCost($toPay);
                $this->transactionService->save($newTx);
            }

            $needed   -= $toPay;
            $consumed += $toPay;
        }

        return $consumed;
    }

    private function recalcInvoicePaid(int $invoiceId): void
    {
        $invoice = $this->invoiceService->getById($invoiceId);
        if ( ! $invoice) {
            return;
        }

        $allClaims = $this->claimService->getByInvoiceId($invoiceId);
        $totalPaid = 0.0;
        foreach ($allClaims as $c) {
            $totalPaid += (float) ($c->getPaid() ?? 0.0);
        }
        $invoice->setPaid($totalPaid);
        $this->invoiceService->save($invoice);
    }
}
